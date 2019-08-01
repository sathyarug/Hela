<?php

namespace App\Http\Controllers\Merchandising\Costing;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use App\Models\Merchandising\Costing\Costing;
use App\Models\Merchandising\Costing\BulkCostingApproval;
use App\Models\Merchandising\Costing\BulkCostingDetails;
use App\Models\Merchandising\Costing\BulkCostingFeatureDetails;
use App\Models\Merchandising\Costing\CostingBulkRevision;
use App\Models\Merchandising\Costing\CostingFinishGood;
use App\Models\Merchandising\Costing\CostingFinishGoodComponent;
use App\Models\Merchandising\HisBulkCosting;
use App\Models\Merchandising\HisBulkCostingDetails;
use App\Models\Merchandising\HisBulkCostingFeatureDetails;
use App\Models\Merchandising\StyleProductFeature;
use App\Models\Finance\Cost\FinanceCost;
use App\Models\Merchandising\StyleCreation;
use App\Models\Merchandising\ProductFeature;
use App\Models\Merchandising\ProductFeatureComponent;
use App\Models\Merchandising\Costing\CostingFinishGoodComponentItem;
use App\Models\Org\UOM;
use App\Models\Org\Color;


class CostingController extends Controller {


    public function index(Request $request) {
        $type = $request->type;
        if ($type == 'getStyleData') {
            return response($this->getStyleData($request->style_id));
        }
        elseif ($type == 'getColorForDivision'){
            $division_id = $request->division_id;
            $query = $request->query;
            return response($this->getColorForDivision($division_id,$query));
        }
        elseif ($type == 'getFinishGood') {
            return response($this->getFinishGood($request->id));
        }
        elseif ($type == 'finance_cost'){
            return response($this->finance_cost());
        }
        elseif ($type == 'total_smv'){
            return response([
              'data' => $this->total_smv($request->style_id, $request->bom_stage_id, $request->color_type_id)
            ]);
        }
        elseif ($type == 'artical_numbers') {
          $search = $request->search;
          return response([
            'data' => $this->get_artical_numbers($search)
          ]);
        }
        elseif ($type == 'item_uom') {
          $item_description = $request->item_description;
          return response([
            'data' => $this->get_item_uom($item_description)
          ]);
        }
        else if($type == 'datatable') {
            $data = $request->all();
            $this->datatable_search($data);
        }
        /*elseif($type == 'getCostListing'){
            return response($this->getCostSheetListing($request->style_id));
        }*/
        /*elseif($type == 'getCostingHeader'){
            return response($this->getCostingHeaderDetails($request->costing_id));
        } */

        /*elseif ($type == 'revision') {
            $data=array('blkNo'=>$request->blk,'bom'=>$request->bom,'season'=>$request->sea,'colType'=>$request->col);
            return response($this->revision($request->style_id,$data));
        }*/
        /*elseif ($type == 'item'){
            $search = $request->search;
            return response($this->getItemList($search));
        }*/
        /*elseif ($type == 'getItemData'){
            $item = $request->item;
            return response($this->getItemDetails($item));
        }*/

        /*elseif ($type == 'getColorForDivisionCode'){
            $division_id = $request->division_id;
            $query = $request->query;
            return response($this->getColorForDivisionCode($division_id,$query));
        }*/
        /*elseif ($type == 'apv'){
            $this->Approval($request);
        }
        /*elseif ($type == 'report-balk'){
            $this->reportBalk($request);
        }*/
        /*elseif ($type == 'report-flash'){
            $this->reportFlash($request);
        }*/
        //new functions

    }


    public function store(Request $request) {
        $costing_count = Costing::where('style_id', '=', $request->style_id)->where('bom_stage_id', '=', $request->bom_stage_id)
        ->where('season_id', '=', $request->season_id)->where('color_type_id', '=', $request->color_type_id)->count();

        if($costing_count > 0){ //chek costing already exixts for same style, bom stage, season and color type
          return response(['data' => [
              'status' => 'error',
              'message' => 'Duplicate costing'
            ]
          ]);
        }
        else
        {
          $costing = new Costing();

          if ($costing->validate($request->all())) { //validate costing details
             //fill data -> style_id, bom_stage_id, season_id, color_type_id, total_order_qty, fob, planned_efficiency, cost_per_std_min,
             //pcd, cost_per_std_min, upcharge, upcharge_reason
              $costing->fill($request->all());
              $pcd_date = date_create($costing->pcd);
              $costing->pcd = date_format($pcd_date,"Y-m-d");//change pcd date format to save in database

              //chek finance details
              $current_timestamp = date("Y-m-d H:i:s");
              $finance_details = FinanceCost::where('effective_from', '<=', $current_timestamp)
              ->where('effective_to', '>=', $current_timestamp)
              ->where('status', 1)->first();

              $finance_charges = 0;
              $cpm_front_end = 0;
              $cpum = 0;
              $cpm_factory = 0;

              if ($finance_details != false && $finance_details != null) { //if has finance details
                 $finance_charges = $finance_details['finance_cost'];
                 $cpm_front_end = $finance_details['cpmfront_end'];
                 $cpum = $finance_details['cpum'];
                 $cpm_factory = $cpum * $costing->planned_efficiency;
              }

              $total_smv = $this->get_total_smv($costing->style_id, $costing->bom_stage_id, $costing->color_type_id);//get smv details
              $labour_cost = $total_smv * $cpm_factory;//calculate labour cost
              $coperate_cost = $total_smv * $cpm_front_end;//calculate coperate cost

              $costing->finance_charges = $finance_charges;
              $costing->finance_cost = 0; //finance cost = total rm cost * finance charges
              $costing->cpm_front_end = $cpm_front_end;
              $costing->cost_per_utilised_min = $cpum;
              $costing->total_smv = $total_smv;
              $costing->cpm_factory = $cpm_factory;
              $costing->labour_cost = $labour_cost;
              $costing->coperate_cost = $coperate_cost;
              $costing->revision_no = 1;
              $costing->status = 'CREATE';
              $costing->save();
              //get product feature components from style
              $finish_goods = $this->get_finish_good($costing->style_id, $costing->bom_stage_id, $costing->color_type_id);
              //send response
              return response(['data' => [
                  'status' => 'success',
                  'message' => 'Costing saved successfully',
                  'costing' => $costing,
                  'feature_component_count' => sizeof($finish_goods),
                  'finish_goods' => $finish_goods
                ]
              ], Response::HTTP_CREATED);
          } else {
              $errors = $costing->errors(); // failure, get errors
              return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
          }
        }
    }


    public function show($id) {

       $costing = Costing::with(['style', 'bom_stage', 'season', 'color_type'])->find($id);
       $finish_goods_count = CostingFinishGood::where('costing_id', '=', $costing->id)->count();
       $finish_goods = [];

       if($finish_goods_count > 0){ //chek already saved finishgood
         $finish_goods = $this->get_saved_finish_good($id); //get saved finish good details
       }
       else{
         $finish_goods = $this->get_finish_good($costing->style_id, $costing->bom_stage_id, $costing->color_type_id); //get finishgood data from smv table
       }
        $feature_component_count = 0;
        if(sizeof($finish_goods) > 0){ //get component count of a product feature
          $feature_component_count = ProductFeature::find($finish_goods[0]->product_feature_id)->count;
        }
        return response([
          'data' => [
            'costing' => $costing,
            'finish_goods' => $finish_goods,
            'feature_component_count' => $feature_component_count,
            ]
        ]);
    }


    public function update(Request $request, $id){
      $costing = Costing::find($id);
      //check costing status. cannot update PENDING and REJECTED costings
      if($costing->status == 'PENDING' || $costing->status == 'REJECTED') {
        return response([
          'data' => [
            'status' => 'error',
            'message' => 'Cannot update '. $costing->status .'costing'
          ]
        ]);
      }
      else {
        $style = StyleCreation::find($costing->style_id);
        $product_feature = ProductFeature::find($style->product_feature_id);
        $finish_goods = $request->finish_goods;//get finisg goods list

        if($costing->status == 'APPROVED'){ //if costing is an APPROVED one, add revision data to history table and save new data as a new revision
          $this->save_costing_revision($costing->id, $costing->revision_no, $request->revision_reason);
          $costing->revision_no = ($costing->revision_no + 1); //update revision no
          $costing->status = 'CREATE'; //clear approval details and save as a new revision
          $costing->approval_user = null;
          $costing->approval_date = null;
          $costing->approval_sent_date = null;
          $costing->approval_sent_user = null;
        }

        $costing->total_order_qty = $request->total_order_qty;
        $costing->fob = $request->fob;
        $costing->planned_efficiency = $request->planned_efficiency;
        $costing->cost_per_std_min = $request->cost_per_std_min;
        $costing->pcd = $request->pcd;
        $costing->upcharge = $request->upcharge;
        $costing->upcharge_reason = $request->upcharge_reason;

        $cpm_factory = $costing->cost_per_utilised_min * $costing->planned_efficiency;
        $labour_cost = $costing->total_smv * $cpm_factory;
        // no need to update corperate cost. Because it will not change based on user inut data
        $costing->cpm_factory = $cpm_factory;
        $costing->labour_cost = $labour_cost;
        $costing->save();

        $current_pack = -1;
        for($x = 0 ; $x < sizeof($finish_goods) ; ($x = $x + $product_feature->count)){
            $fg = null;
            if($finish_goods[$x]['fg_id'] == 0){ //new finish good
              $fg = new CostingFinishGood();
              $fg->costing_id = $costing->id;
            }
            else{
              $fg = CostingFinishGood::find($finish_goods[$x]['fg_id']);// get existing finish good
            }

            if($finish_goods[$x]['combo_color'] != null && $finish_goods[$x]['combo_color'] != '') {//get combo color if exists
              $combo_color = Color::where('color_name', '=', $finish_goods[$x]['combo_color'])->first();
              $fg->combo_color_id = $combo_color->color_id;
            }
            else {// no combo color
              $fg->combo_color_id = null;
            }

            $fg->pack_no = $finish_goods[$x]['pack_no'];
            $fg->product_feature = $finish_goods[$x]['product_feature_id'];
            $fg->epm = 0;
            $fg->np = 0;
            $fg->save();

            for($y = $x ; $y < ($x + $product_feature->count) ; $y++) {
              if($finish_goods[$y]['id'] == 0){ //new component
                $finish_good_component = new CostingFinishGoodComponent();
                $finish_good_component->fg_id = $fg->fg_id;
              }
              else{
                $finish_good_component = CostingFinishGoodComponent::find($finish_goods[$y]['id']);
              }

              if($finish_goods[$y]['color'] != null && $finish_goods[$y]['color'] != '') {//get garment color if exists
                $color = Color::where('color_name', '=', $finish_goods[$y]['color'])->first();
                $finish_good_component->color_id = $color->color_id;
              }
              else {//no garment color
                $finish_good_component->color_id = null;
              }

              $finish_good_component->product_component_id = $finish_goods[$y]['product_component_id'];
              $finish_good_component->product_silhouette_id = $finish_goods[$y]['product_silhouette_id'];
              $finish_good_component->line_no = $finish_goods[$y]['line_no'];
              $finish_good_component->surcharge = $finish_goods[$y]['surcharge'];
              $finish_good_component->mcq = $finish_goods[$y]['mcq'];
              $finish_good_component->smv = $finish_goods[$y]['smv'];
              $finish_good_component->status = 1;
              $finish_good_component->save();
            }
          }
          return response([
            'data' => [
              'status' => 'success',
              'message' => 'Costing updated successfully',
              'costing' => $costing,
              'finish_goods' => $this->get_saved_finish_good($costing->id),
              'feature_component_count' => $product_feature->count,
            ]
          ]);
      }
    }


    public function destroy($id) {

    }


    private function getStyleData($style_id) {
        $dataArr = array();
        $styleData = StyleCreation::find($style_id);
        //$hader = Costing::where('style_id', $style_id)->get()->toArray();
        //$country = \App\Models\Org\Country::find($styleData->customer->customer_country);

        $dataArr['remark_style'] = $styleData->remark_style;
        $dataArr['division_name'] = $styleData->division->division_description;
        $dataArr['division_id'] = $styleData->division->division_id;
        $dataArr['style_desc'] = $styleData->style_description;
        $dataArr['cust_name'] = $styleData->customer->customer_name;
        $dataArr['style_desc'] = $styleData->style_description;
        $dataArr['style_id'] = $styleData->style_id;
        $dataArr['style_no'] = $styleData->style_no;
        $dataArr['image'] = $styleData->image;

      /*  if(count($hader)>0){
            $costed_smv = 0;
            $blkCostFea = [];

            if(count($blkCostFea)>0){
                $sum=0;
                foreach ($blkCostFea AS $CostFea ){
                    $sum+=$CostFea['smv'];
                }
                $costed_smv=$sum;
            }
            $hader[0]['pcd']=date_format(date_create($hader[0]['pcd']),"m/d/Y");
            $dataArr['blk_hader'] = $hader[0];
            $dataArr['blk_hader']['smv_received']='';
            $dataArr['blk_hader']['costed_smv_id']=$costed_smv;

        }else{
            $financeCost=\App\Models\Finance\Cost\FinanceCost::first();

            $dataArr['blk_hader']['updated_date']='';
            $dataArr['blk_hader']['total_cost']='';
            $dataArr['blk_hader']['season_id']='';
            $dataArr['blk_hader']['color_type_id']='';
            $dataArr['blk_hader']['created_date']='';
            $dataArr['blk_hader']['cost_per_std_min']=$financeCost->cpmfront_end;
            $dataArr['blk_hader']['epm']='';
            $dataArr['blk_hader']['np_margin']='';
            $dataArr['blk_hader']['plan_efficiency']='';
            $dataArr['blk_hader']['bulk_costing_id']='';
            $dataArr['blk_hader']['pcd']='';
            $dataArr['blk_hader']['finance_charges']=$financeCost->finance_cost;
            $dataArr['blk_hader']['cost_per_min']=$financeCost->cpum;

            $dataArr['blk_hader']['costed_smv_id']=0;

            $dataArr['blk_hader']['costing_status']=0;

        }*/
        return $dataArr;
    }


    private function getFinishGood($id) {
      return [
        'finish_goods' => $this->get_saved_finish_good($id)
      ];
    }

//    private function getEmpNp($product_feature_id,$data) {
//
//        $blk=$data['blkNo'];
//        $bom=$data['bom'];
//        $season=$data['season'];
//        $colType=$data['colType'];
//
//
//        $getTotel=DB::select('SELECT
//Sum((costing_bulk_details.unit_price*costing_bulk_details.gross_consumption)) AS total,
//Sum(costing_bulk_feature_details.smv) AS smv,
//costing_bulk.fob,
//costing_bulk.plan_efficiency
//FROM
//costing_bulk_feature_details
//INNER JOIN costing_bulk_details ON costing_bulk_details.bulkheader_id = costing_bulk_feature_details.blk_feature_id
//INNER JOIN costing_bulk ON costing_bulk.bulk_costing_id = costing_bulk_feature_details.bulkheader_id
//WHERE costing_bulk.bulk_costing_id='.$blk.' AND costing_bulk_feature_details.style_feature_id='.$product_feature_id.' AND costing_bulk_feature_details.season_id='.$season.' AND costing_bulk_feature_details.col_opt_id='.$colType.' AND costing_bulk_feature_details.bom_stage='.$bom.' AND costing_bulk_details.status=1');
//
//        $rmCost=0;$smv=0;$fob=0;$epm=0;$labourCost=0;$cpm=0;$totalManuf=0;$finCost=0;$copCost=0;$totalCost=0;$np=0;
//        if($getTotel[0]->total!=''){
//            $rmCost=$getTotel[0]->total;
//        }
//        if($getTotel[0]->smv!=''){
//            $smv=$getTotel[0]->smv;
//        }
//        if($getTotel[0]->fob!=''){
//            $fob=$getTotel[0]->fob;
//        }
//        if($smv !=0){
//            $epm=($fob-$rmCost)/$smv;
//        }
//
//        $financeCost=\App\Models\Finance\Cost\FinanceCost::first();
//        $cpm=($getTotel[0]->plan_efficiency*$financeCost->cpum);
//        $labourCost=$smv*$cpm;
//        $totalManuf=$rmCost+$labourCost;
//        $finCost=$financeCost->finance_cost;
//        $copCost=$smv*$financeCost->cpmfront_end;
//        $totalCost=$rmCost+$totalManuf+$finCost+$copCost;
//
//        if($totalCost !=0){
//            $np=($totalCost-$fob)/$totalCost;
//        }
//
//        return array('epm'=>$epm,'np'=>$np);
//
//    }


    public function send_to_approval(Request $request) {
        $user = auth()->user();
        $costing = Costing::find($request->costing_id);
        $costing->status = 'PENDING';
        $costing->approval_user = 19;
        $costing->approval_sent_user = $user->user_id;
        $costing->approval_sent_date = date("Y-m-d H:i:s");
        $costing->save();

        return response([
          'data' => [
            'status' => 'success',
            'message' => 'Costing sent for approval',
            'costing' => $costing
          ]
        ]);
    }

    /*private function revision($style_id,$data) {
        $max=CostingBulkRevision::where('costing_id',$data['blkNo'])->max('revision');
        $newMax=$max+1;

        $CostingBulkRevision = new CostingBulkRevision();
        $CostingBulkRevision->costing_id=$data['blkNo'];
        $CostingBulkRevision->revision=$newMax;
        $CostingBulkRevision->save();
        $blk = Costing::find($data['blkNo']);

        $HisBulkCosting = new HisBulkCosting();
        $HisBulkCosting->revistion_id=$newMax;
        $HisBulkCosting->bulk_costing_id=$blk->bulk_costing_id;
        $HisBulkCosting->style_id=$blk->style_id;
        $HisBulkCosting->pcd=$blk->pcd;
        $HisBulkCosting->plan_efficiency=$blk->plan_efficiency;
        $HisBulkCosting->fob=$blk->fob;
        $HisBulkCosting->finance_charges=$blk->finance_charges;
        $HisBulkCosting->cost_per_min=$blk->cost_per_min;
        $HisBulkCosting->cost_per_std_min=$blk->cost_per_std_min;
        $HisBulkCosting->epm=$blk->epm;
        $HisBulkCosting->np_margin=$blk->np_margin;
        $HisBulkCosting->save();

        $BulkCostingFeatureDetails= BulkCostingFeatureDetails::where('bulkheader_id', $data['blkNo'])->get();
//        dd($BulkCostingFeatureDetails);exit;
        foreach($BulkCostingFeatureDetails as $BulkCostingFeatureData){
            $HisBulkCostingFeatureDetails = new HisBulkCostingFeatureDetails();

            $HisBulkCostingFeatureDetails->revistion_id=$newMax;
            $HisBulkCostingFeatureDetails->blk_feature_id=$BulkCostingFeatureData->blk_feature_id;
            $HisBulkCostingFeatureDetails->style_feature_id = $BulkCostingFeatureData->style_feature_id;
            $HisBulkCostingFeatureDetails->feature_id = $BulkCostingFeatureData->feature_id;
            $HisBulkCostingFeatureDetails->component_id = $BulkCostingFeatureData->component_id;
            $HisBulkCostingFeatureDetails->bulkheader_id = $BulkCostingFeatureData->bulkheader_id;
            $HisBulkCostingFeatureDetails->surcharge = $BulkCostingFeatureData->surcharge;
            $HisBulkCostingFeatureDetails->color_ID = $BulkCostingFeatureData->color_ID;
            $HisBulkCostingFeatureDetails->season_id = $BulkCostingFeatureData->season_id;
            $HisBulkCostingFeatureDetails->col_opt_id = $BulkCostingFeatureData->col_opt_id;
            $HisBulkCostingFeatureDetails->bom_stage = $BulkCostingFeatureData->bom_stage;
            $HisBulkCostingFeatureDetails->mcq = $BulkCostingFeatureData->mcq;
            $HisBulkCostingFeatureDetails->combo_code = $BulkCostingFeatureData->combo_code;
            $HisBulkCostingFeatureDetails->combo_color = $BulkCostingFeatureData->combo_color;
            $HisBulkCostingFeatureDetails->smv = $BulkCostingFeatureData->smv;
            $HisBulkCostingFeatureDetails->save();

            $itemList = BulkCostingDetails::where('bulkheader_id', $BulkCostingFeatureData->blk_feature_id)->get();

            foreach($itemList as $item){
                $HisBulkCostingDetails = new HisBulkCostingDetails();

                $HisBulkCostingDetails->revistion_id=$newMax;
                $HisBulkCostingDetails->item_id=$item->item_id;
                $HisBulkCostingDetails->bulkheader_id = $item->bulkheader_id;
                $HisBulkCostingDetails->article_no = $item->article_no;
                $HisBulkCostingDetails->color_id = $item->color_id;
                $HisBulkCostingDetails->color_type_id = $item->color_type_id;
                $HisBulkCostingDetails->code = $item->code;
                $HisBulkCostingDetails->main_item = $item->main_item;
                $HisBulkCostingDetails->supplier_id = $item->supplier_id;
                $HisBulkCostingDetails->position = $item->position;
                $HisBulkCostingDetails->measurement = $item->measurement;
                $HisBulkCostingDetails->process_option = $item->process_option;
                $HisBulkCostingDetails->uom_id = $item->uom_id;
                $HisBulkCostingDetails->net_consumption = $item->net_consumption;
                $HisBulkCostingDetails->unit_price = $item->unit_price;
                $HisBulkCostingDetails->wastage = $item->wastage;
                $HisBulkCostingDetails->gross_consumption = $item->gross_consumption;
                $HisBulkCostingDetails->freight_charges = $item->freight_charges;
                $HisBulkCostingDetails->finance_charges = $item->finance_charges;
                $HisBulkCostingDetails->mcq = $item->mcq;
                $HisBulkCostingDetails->moq = $item->moq;
                $HisBulkCostingDetails->calculate_by_deliverywise = $item->calculate_by_deliverywise;
                $HisBulkCostingDetails->order_type = $item->order_type;
                $HisBulkCostingDetails->surcharge = $item->surcharge;
                $HisBulkCostingDetails->total_cost = $item->total_cost;
                $HisBulkCostingDetails->shipping_terms = $item->shipping_terms;
                $HisBulkCostingDetails->lead_time = $item->lead_time;
                $HisBulkCostingDetails->country_of_origin = $item->country_of_origin;
                $HisBulkCostingDetails->comments = $item->comments;
                $HisBulkCostingDetails->save();

            }
        }
        $blk = Costing::find($data['blkNo']);
        $blk->costing_status='Edit';
        $blk->save();
        return($this->getFinishGood($style_id,$data));
    }*/


    /*private function saveLineHeader($request,$data){
        $color=\App\Models\Org\Color::where('color_name', $request->color)->first();
        $color_combo=\App\Models\Org\Color::where('color_code', $request->color_combo)->first();


 if($request->blkHead != 0){
     $bulkCostingDetails = BulkCostingFeatureDetails::find($request->blkHead);
 }else{
     $bulkCostingDetails = new BulkCostingFeatureDetails();
 }

        $bulkCostingDetails->bulkheader_id=$request->blkHead;
        $bulkCostingDetails->color_id=$color->color_id;
        $bulkCostingDetails->combo_color=$color_combo->color_id;
        $bulkCostingDetails->feature_id=$request->main_featureName_id;
        $bulkCostingDetails->component_id=$request->id;
        $bulkCostingDetails->mcq=$request->mcq;
        $bulkCostingDetails->smv=$request->smv;
        $bulkCostingDetails->surcharge=$request->surcharge;
        $bulkCostingDetails->style_feature_id=$request->style_feature_id;
        $bulkCostingDetails->bulkheader_id=$data['blkNo'];
        $bulkCostingDetails->bom_stage=$data['bom'];
        $bulkCostingDetails->season_id=$data['season'];
        $bulkCostingDetails->col_opt_id=$data['colType'];
        $bulkCostingDetails->save();

        $styleFeatures=BulkCostingFeatureDetails::where('style_feature_id',$bulkCostingDetails->style_feature_id)->where('feature_id',$bulkCostingDetails->feature_id)->where('status',1)->get();

        foreach ($styleFeatures AS $Features){
            $bulkCostingDetailsUpdate = BulkCostingFeatureDetails::find($Features->blk_feature_id);
            $bulkCostingDetailsUpdate->combo_color=$color_combo->color_id;
            $bulkCostingDetailsUpdate->save();
        }

        $model = Costing::find($bulkCostingDetails->bulkheader_id);

        $data=array('blkNo'=>$data['blkNo'],'bom'=>$data['bom'],'season'=>$data['season'],'colType'=>$data['colType']);

        return $this->getFinishGood($model->style_id,$data);

//        return $bulkCostingDetails->blk_feature_id;

}*/

    /*public function getItemList($search){
        return \App\itemCreation::select('master_id', 'master_description')
            ->where([['master_description', 'like', '%' . $search . '%'],])->get();
    }*/

    /*public function getItemDetails($id){
        $master= \App\itemCreation::find($id)->toArray();
        $SubCategory= \App\Models\Finance\Item\SubCategory::find($master['subcategory_id'])->toArray();
        $category= \App\Models\Finance\Item\Category::find($SubCategory['category_id'])->toArray();
        $supplier= \App\Models\Org\Supplier::where('status', 1)->get()->toArray();
        $serviceType= \App\Models\IE\ServiceType::where('status', 1)->get()->toArray();

        return array('category'=>$category,'supplier'=>$supplier,'pOptions'=>$serviceType);
    }*/

    public  function getColorForDivision($division_id,$query){
//        $color=\App\Models\Org\Color::where([['division_id','=',$division_id]])->pluck('color_name')->toArray();
        $color=\App\Models\Org\Color::pluck('color_name')->toArray();
        return json_encode($color);
    }

    /*public  function getColorForDivisionCode($division_id,$query){
//        $color=\App\Models\Org\Color::where([['division_id','=',$division_id]])->pluck('color_code')->toArray();
        $color=\App\Models\Org\Color::pluck('color_code')->toArray();
        return json_encode($color);
    }*/


    /*public  function Approval($request){
        $BulkCosting = Costing::find($request->blk);
        if(isset($request->ur)){
            if($BulkCosting->costing_status =='SentToApproval'){
               //$CostingBulkRevision=BulkCostingApproval::where('costing_id',$request->blk)->where('approval_key',$request->approval_key)->where('id',$request->aid)->get();
                $blkApp = BulkCostingApproval::find($request->aid);
                $blkApp->lock_status=1;
                $blkApp->save();

                $upate =DB::table('costing')
                    ->where('bulk_costing_id', $blkApp->costing_id)
                    ->update(['costing_approval_user' => $request->ur,'costing_approval_time'=>now(),'costing_status'=>'approved']);
                    //$user = auth()->user();
            }else{
            }
        }
    }*/


    /*public  function reportBalk($request){

        $getAllData=DB::select('SELECT
item_master.master_description,
item_master.master_code,
item_subcategory.subcategory_name,
item_category.category_name,
costing.style_id,
costing.bulk_costing_id,
costing_bulk_feature_details.blk_feature_id,
costing_bulk_details.item_id,
costing.pcd,
costing.plan_efficiency,
costing.fob,
costing.finance_charges,
costing.cost_per_min,
costing.cost_per_std_min,
costing.epm,
costing.np_margin,
sum((costing_bulk_details.unit_price*costing_bulk_details.gross_consumption)) AS total,
\'\' AS updated_date,
\'\' AS User
FROM
costing_bulk_details
INNER JOIN item_master ON item_master.master_id = costing_bulk_details.main_item
INNER JOIN item_subcategory ON item_master.subcategory_id = item_subcategory.subcategory_id
INNER JOIN item_category ON item_category.category_id = item_subcategory.category_id
INNER JOIN costing_bulk_feature_details ON costing_bulk_feature_details.blk_feature_id = costing_bulk_details.bulkheader_id
INNER JOIN costing ON costing_bulk_feature_details.bulkheader_id = costing.bulk_costing_id
WHERE
costing.style_id ='.$request->style_id.'
GROUP BY
item_category.category_name');

        $getAllDataHis=DB::select('SELECT
costing.style_id,
costing_bulk_revision.revision,
his_costing_bulk.pcd,
his_costing_bulk_feature_details.blk_feature_id,
his_costing_bulk_details.bulkheader_id,
item_master.master_description,
item_subcategory.subcategory_name,
item_category.category_name,
his_costing_bulk.plan_efficiency,
his_costing_bulk.fob,
his_costing_bulk.finance_charges,
his_costing_bulk.cost_per_min,
his_costing_bulk.cost_per_std_min,
his_costing_bulk.epm,
his_costing_bulk.np_margin,
Sum((his_costing_bulk_details.unit_price*his_costing_bulk_details.gross_consumption)) AS total,
costing_bulk_revision.created_date,
costing_bulk_revision.updated_date,
CONCAT(usr_profile.first_name, " ", usr_profile.last_name, "-", costing_bulk_revision.created_by) AS User
FROM
costing
INNER JOIN costing_bulk_revision ON costing.bulk_costing_id = costing_bulk_revision.costing_id
INNER JOIN his_costing_bulk ON costing_bulk_revision.revision = his_costing_bulk.revistion_id AND costing_bulk_revision.costing_id = his_costing_bulk.bulk_costing_id
INNER JOIN his_costing_bulk_feature_details ON his_costing_bulk.revistion_id = his_costing_bulk_feature_details.revistion_id AND his_costing_bulk.bulk_costing_id = his_costing_bulk_feature_details.bulkheader_id
INNER JOIN his_costing_bulk_details ON his_costing_bulk_feature_details.revistion_id = his_costing_bulk_details.revistion_id AND his_costing_bulk_feature_details.blk_feature_id = his_costing_bulk_details.bulkheader_id
INNER JOIN item_master ON item_master.master_id = his_costing_bulk_details.item_id
INNER JOIN item_subcategory ON item_master.subcategory_id = item_subcategory.subcategory_id
INNER JOIN item_category ON item_subcategory.category_id = item_category.category_id
INNER JOIN usr_profile ON usr_profile.user_id = costing_bulk_revision.created_by
WHERE
costing.style_id='.$request->style_id.'
GROUP BY
costing_bulk_revision.revision,item_category.category_name
ORDER BY
costing_bulk_revision.revision DESC
');


        $data=array();
        $fullData=array();
        $index=0;

        $fabric=$trims=$packing=$other='';
        foreach ($getAllData AS $allData){
            if($allData->category_name == 'Fabric'){
                $fabric=$allData->total;
            }
            if($allData->category_name == 'Trims'){
                $trims=$allData->total;
            }
            if($allData->category_name == 'Packing'){
                $packing=$allData->total;
            }
            if($allData->category_name == 'Other'){
                $other=$allData->total;
            }

        }

// print_r(count($getAllData));exit;
if(count($getAllData)>0){
	$data=array(
                'pcd'=>$getAllData[0]->pcd,
                'plan_efficiency'=>$getAllData[0]->plan_efficiency,
                'fob'=>$getAllData[0]->fob,
                'finance_charges'=>$getAllData[0]->finance_charges,
                'cost_per_min'=>$getAllData[0]->cost_per_min,
                'cost_per_std_min'=>$getAllData[0]->cost_per_std_min,
                'epm'=>$getAllData[0]->epm,
                'np_margin'=>$getAllData[0]->np_margin,
                'fabric'=>$fabric,
                'trims'=>$trims,
                'packing'=>$packing,
                'other'=>$other,
                'updated_date'=>$getAllData[0]->updated_date,
                'User'=>$getAllData[0]->User

            );

        $fullData[$index]=$data;
}


         if(count($getAllDataHis)>0){

         	 $one=0;$two=$getAllDataHis[0]->revision;
        $fabric=$trims=$packing=$other='';
        foreach ($getAllDataHis AS $allDataHis){
            $one=$allDataHis->revision;
            $data=array();
           // dd($allDataHis->revistion_id);

            if($allDataHis->category_name == 'Fabric'){
                $fabric=$allData->total;
            }
            if($allDataHis->category_name == 'Trims'){
                $trims=$allData->total;
            }
            if($allDataHis->category_name == 'Packing'){
                $packing=$allData->total;
            }
            if($allDataHis->category_name == 'Other'){
                $other=$allData->total;
            }
            $data=array(
                'pcd'=>$allDataHis->pcd,
                'plan_efficiency'=>$allDataHis->plan_efficiency,
                'fob'=>$allDataHis->fob,
                'finance_charges'=>$allDataHis->finance_charges,
                'cost_per_min'=>$allDataHis->cost_per_min,
                'cost_per_std_min'=>$allDataHis->cost_per_std_min,
                'epm'=>$allDataHis->epm,
                'np_margin'=>$allDataHis->np_margin,
                'fabric'=>$fabric,
                'trims'=>$trims,
                'packing'=>$packing,
                'other'=>$other,
                'updated_date'=>$allDataHis->updated_date,
                'User'=>$allDataHis->User

            );
            if($one !=$two){
                $index++;
                $fullData[$index]=$data;
                $two=$one;

            }
        }
         }


        $styleData = \App\Models\Merchandising\StyleCreation::find($request->style_id);
        //dd($styleData->image);
        print_r(json_encode(array('image'=>$styleData->image,'data'=>$fullData)));exit;

    }*/

    /*public  function reportFlash($request){
        $styleData = \App\Models\Merchandising\StyleCreation::find($request->style_id);
        $flashHaderData = \App\Models\Merchandising\Costing\Flash\cost_flash_header::find($request->style_id);


        $getAllDataFlash=DB::select('SELECT
item_master.master_id,
cost_flash_header.style_id,
cost_flash_details.req_qty,
cost_flash_details.tot_req_qty,
cost_flash_details.total_value,
item_category.category_name,
item_category.category_id,
item_subcategory.subcategory_name,
item_master.master_description,
item_master.master_code
FROM
cost_flash_header
INNER JOIN cost_flash_details ON cost_flash_details.costing_id = cost_flash_header.costing_id
INNER JOIN item_master ON item_master.master_id = cost_flash_details.master_id
INNER JOIN item_subcategory ON item_master.subcategory_id = item_subcategory.subcategory_id
INNER JOIN item_category ON item_subcategory.category_id = item_category.category_id
WHERE cost_flash_header.style_id = '.$request->style_id.'
ORDER BY item_category.category_id');

        print_r(json_encode(array('image'=>$styleData->image,'data'=>$flashHaderData, 'details'=>$getAllDataFlash)));exit;
    }*/


    /*private function getCostSheetListing($styleId){
        $bulkHeaderData = Costing::select(DB::raw("*, LPAD(bulk_costing_id,6,'0') AS CostingNo"))
        ->where('style_id','=',$styleId)->get();
        //  dd($bulkHeaderData);
        return $bulkHeaderData;
    }*/


    /*private function getCostingHeaderDetails($costingId){
        $costingHeader = \DB::table('costing')
        ->select(DB::raw('costing.*,org_season.season_name,(select sum(merc_costing_so_combine.qty) from merc_costing_so_combine where merc_costing_so_combine.costing_id = costing.bulk_costing_id) AS Tot_Qty'))
        ->distinct()
        ->join('costing_bulk_feature_details','costing.bulk_costing_id','=','costing_bulk_feature_details.bulkheader_id')
        ->join('org_season','org_season.season_id','=','costing_bulk_feature_details.season_id')
        ->where('costing.bulk_costing_id',$costingId)
        ->get();
        return $costingHeader;
    }*/


    public function copy_finish_good(Request $request){
      $fg_id = $request->fg_id;
      $finish_good = CostingFinishGood::find($fg_id);
      $finish_good_copy = $finish_good->replicate();
      $finish_good_copy->pack_no = DB::table('costing_finish_goods')->where('costing_id', '=', $finish_good->costing_id)->max('pack_no') + 1;
      $finish_good_copy->save();

      $components = CostingFinishGoodComponent::where('fg_id', '=', $finish_good->fg_id)->get();
      for($x = 0 ; $x < sizeof($components) ; $x++){
        $component_copy = $components[$x]->replicate();
        $component_copy->fg_id = $finish_good_copy->fg_id;
        $component_copy->save();

        $component_items = CostingFinishGoodComponentItem::where('fg_component_id', '=', $components[$x]['id'])->get();
        for($y = 0 ; $y < sizeof($component_items) ; $y++){
          $component_item_copy = $component_items[$y]->replicate();
          $component_item_copy->fg_component_id = $component_copy->id;
          $component_item_copy->save();
        }
      }

      $finish_goods = $this->get_saved_finish_good($finish_good->costing_id);
      return response([
        'data' => [
          'status' => 'success',
          'message' => 'Finish good copied successfully',
          'feature_component_count' => sizeof($components),
          'finish_goods' => $finish_goods
        ]
      ]);

    }


    public function copy(Request $request){
      $costing = Costing::find($request->costing_id);
      $bom_stage_id = $request->bom_stage_id;
      $season_id = $request->season_id;
      $color_type_id = $request->color_type_id;
    //  echo $bom_stage_id;die();
      $costing_count = Costing::where('style_id', '=', $costing->style_id)->where('bom_stage_id', '=', $bom_stage_id)
      ->where('season_id', '=', $season_id)->where('color_type_id', '=', $color_type_id)->count();

      if($costing_count > 0){
        return response(['data' => [
            'status' => 'error',
            'message' => 'Costing already exists'
          ]
        ]);
      }
      else{
        $costing_copy = $costing->replicate();
        $costing_copy->bom_stage_id = $bom_stage_id;
        $costing_copy->season_id = $season_id;
        $costing_copy->color_type_id;
        $costing_copy->approval_user = null;
        $costing_copy->approval_date = null;
        $costing_copy->approval_sent_date = null;
        $costing_copy->approval_sent_user = null;
        $costing_copy->status = 'CREATE';
        $costing_copy->save();

        $finish_goods = CostingFinishGood::where('costing_id', '=', $costing->id)->get();
        for($x = 0 ; $x < sizeof($finish_goods); $x++){
          $finish_good_copy = $finish_goods[$x]->replicate();
          $finish_good_copy->costing_id = $costing_copy->id;
          $finish_good_copy->save();

          $components = CostingFinishGoodComponent::where('fg_id', '=', $finish_goods[$x]->fg_id)->get();
          for($y = 0 ; $y < sizeof($components) ; $y++){
            $component_copy = $components[$y]->replicate();
            $component_copy->fg_id = $finish_good_copy->fg_id;
            $component_copy->save();

            $component_items = CostingFinishGoodComponentItem::where('fg_component_id', '=', $components[$y]['id'])->get();
            for($z = 0 ; $z < sizeof($component_items) ; $z++){
              $component_item_copy = $component_items[$z]->replicate();
              $component_item_copy->fg_component_id = $component_copy->id;
              $component_item_copy->save();
            }
          }
        }
        return response([
          'data' => [
            'status' => 'success',
            'message' => 'Costing coppied successfully'
          ]
        ]);
      }
    }


    public function delete_finish_good(Request $request){
      $fg_id = $request->fg_id;
      $finish_good = CostingFinishGood::find($fg_id);

      $components = CostingFinishGoodComponent::where('fg_id', '=', $finish_good->fg_id)->get();
      for($x = 0 ; $x < sizeof($components) ; $x++){
        CostingFinishGoodComponentItem::where('fg_component_id', '=', $components[$x]['id'])->delete();
      }

      CostingFinishGoodComponent::where('fg_id', '=', $finish_good->fg_id)->delete();
      $finish_good->delete();

      return response([
        'data' => [
          'message' => 'Finisg good deleted successfully.',
          'feature_component_count' => sizeof($components),
          'finish_goods' => $this->get_saved_finish_good($finish_good->costing_id)
        ]
      ] , Response::HTTP_OK);
    }


    //***********************************************************

    private function finance_cost(){
      $current_timestamp = date("Y-m-d H:i:s");
      $finance_details = FinanceCost::where('effective_from', '<=', $current_timestamp)
      ->where('effective_to', '>=', $current_timestamp)
      ->where('status', 1)->first();

      if($finance_details != null && $finance_details != false){
        return [
          'status' => 'success',
          'finance_details' => $finance_details
        ];
      }
      else{
        return [
          'status' => 'error',
          'message' => 'Finance details not avaliable. Contact finace team.'
        ];
      }
    }


    private function total_smv($style_id, $bom_stage_id, $color_type_id) {
      $total_smv = DB::table('ie_component_smv_header')->where('style_id', '=', $style_id)
      ->where('bom_stage_id', '=', $bom_stage_id)
      ->where('col_opt_id', '=', $color_type_id)
      ->where('status', '=', 1)->first();

      if($total_smv == null || $total_smv == false){
        return [
          'status' => 'error',
          'message' => 'SMV details not avaliable. Contact IE team.'
        ];
      }
      else{
        return [
          'status' => 'success',
          'total_smv' => $total_smv->total_smv
        ];
      }
    }


    private function get_total_smv($style_id, $bom_stage_id, $color_type_id){
      $total_smv = DB::table('ie_component_smv_header')->where('style_id', '=', $style_id)
      ->where('bom_stage_id', '=', $bom_stage_id)
      ->where('col_opt_id', '=', $color_type_id)
      ->where('status', '=', 1)->first();

      if($total_smv == null || $total_smv == false){
        return 0;
      }
      else{
        return $total_smv->total_smv;
      }
    }

    private function calculate_fg_component_rm_cost($fg_component_id){
      $cost = CostingFinishGoodComponentItem::where('fg_component_id', '=', $fg_component_id)->sum('total_cost');
      return $cost;
    }

    private function calculate_fg_rm_cost($fg_id){
      $cost = CostingFinishGoodComponentItem::join('costing_finish_good_components', 'costing_finish_good_components.id', '=', 'costing_finish_good_component_items.fg_component_id')
      ->where('costing_finish_good_components.id', '=', $fg_id)->sum('costing_finish_good_component_items.total_cost');
      return $cost;
    }


    private function get_finish_good($style_id, $bom_stage, $color_type) {
      $product_feature_components = DB::select("SELECT
        ie_component_smv_summary.product_component_id,
        ie_component_smv_summary.product_silhouette_id,
        ie_component_smv_summary.line_no,
        ie_component_smv_summary.total_smv AS smv,
        product_feature.product_feature_id,
        product_component.product_component_description,
        product_silhouette.product_silhouette_description,
        product_feature.product_feature_description,
        '0' AS mcq,
        '0' AS surcharge,
        '0' AS epm,
        '0' AS np,
        '0' AS id,
        '1' AS pack_no,
        '' AS combo_color,
        '' AS color,
        '0' AS fg_id
        FROM ie_component_smv_summary
        INNER JOIN ie_component_smv_header ON ie_component_smv_header.smv_component_header_id = ie_component_smv_summary.smv_component_header_id
        INNER JOIN product_component ON product_component.product_component_id = ie_component_smv_summary.product_component_id
        INNER JOIN product_silhouette ON product_silhouette.product_silhouette_id = ie_component_smv_summary.product_silhouette_id
        INNER JOIN product_feature ON product_feature.product_feature_id = ie_component_smv_header.product_feature_id
        WHERE ie_component_smv_header.style_id = ?
        AND ie_component_smv_header.bom_stage_id = ?
        AND ie_component_smv_header.col_opt_id = ?
        AND ie_component_smv_header.status = ? ", [$style_id, $bom_stage, $color_type, 1]);

        return $product_feature_components;
    }


    private function get_saved_finish_good($id){
      $costing = Costing::find($id);
      //$style = StyleCreation::find($costing->style_id);

      $product_feature_components = DB::select("SELECT
        costing_finish_good_components.*,
        costing_finish_goods.pack_no,
        costing_finish_goods.combo_color_id,
        costing_finish_goods.epm,
        costing_finish_goods.np,
        product_feature.product_feature_id,
        product_component.product_component_description,
        product_silhouette.product_silhouette_description,
        product_feature.product_feature_description,
        color1.color_name AS combo_color,
        color2.color_name AS color
        FROM costing_finish_good_components
        INNER JOIN costing_finish_goods ON costing_finish_goods.fg_id = costing_finish_good_components.fg_id
        INNER JOIN product_component ON product_component.product_component_id = costing_finish_good_components.product_component_id
        INNER JOIN product_silhouette ON product_silhouette.product_silhouette_id = costing_finish_good_components.product_silhouette_id
        INNER JOIN product_feature ON product_feature.product_feature_id = costing_finish_goods.product_feature
        LEFT JOIN org_color AS color1 ON color1.color_id = costing_finish_goods.combo_color_id
        INNER JOIN org_color AS color2 ON color2.color_id = costing_finish_good_components.color_id
        WHERE costing_finish_goods.costing_id = ? ", [$id]);

        return $product_feature_components;
    }


    private function datatable_search($data)
    {
          $start = $data['start'];
          $length = $data['length'];
          $draw = $data['draw'];
          $search = $data['search']['value'];
          $order = $data['order'][0];
          $order_column = $data['columns'][$order['column']]['data'];
          $order_type = $order['dir'];

          $costing_list = Costing::select('costing.*','style_creation.style_no','merc_bom_stage.bom_stage_description',
            'org_season.season_name', 'merc_color_options.color_option')
          ->join('style_creation', 'style_creation.style_id', '=', 'costing.style_id')
          ->join('merc_bom_stage', 'merc_bom_stage.bom_stage_id', '=', 'costing.bom_stage_id')
          ->join('org_season', 'org_season.season_id', '=', 'costing.season_id')
          ->join('merc_color_options', 'merc_color_options.col_opt_id', '=', 'costing.color_type_id')
          ->where('costing.id'  , 'like', $search.'%' )
          ->orWhere('style_creation.style_no'  , 'like', $search.'%' )
          ->orWhere('merc_bom_stage.bom_stage_description','like',$search.'%')
          ->orWhere('org_season.season_name','like',$search.'%')
          ->orWhere('merc_color_options.color_option','like',$search.'%')
          ->orderBy($order_column, $order_type)
          ->offset($start)->limit($length)->get();

          $costing_count = Costing::join('style_creation', 'style_creation.style_id', '=', 'costing.style_id')
          ->join('merc_bom_stage', 'merc_bom_stage.bom_stage_id', '=', 'costing.bom_stage_id')
          ->join('org_season', 'org_season.season_id', '=', 'costing.season_id')
          ->join('merc_color_options', 'merc_color_options.col_opt_id', '=', 'costing.color_type_id')
          ->where('costing.id'  , 'like', $search.'%' )
          ->orWhere('style_creation.style_no'  , 'like', $search.'%' )
          ->orWhere('merc_bom_stage.bom_stage_description','like',$search.'%')
          ->orWhere('org_season.season_name','like',$search.'%')
          ->orWhere('merc_color_options.color_option','like',$search.'%')
          ->count();

          echo json_encode([
              "draw" => $draw,
              "recordsTotal" => $costing_count,
              "recordsFiltered" => $costing_count,
              "data" => $costing_list
          ]);
    }


    private function get_artical_numbers($search){
      $list = DB::table('costing_finish_good_component_items')->where('article_no', 'like', $search . '%')
      ->distinct('article_no')->get()->pluck('article_no');
      return $list;
    }

    private function get_item_uom($item_description){
      $list = DB::table('item_uom')
      ->join('org_uom', 'org_uom.uom_id', '=', 'item_uom.uom_id')
      ->join('item_master', 'item_master.master_id', '=', 'item_uom.master_id')
      ->where('item_master.master_description', '=', $item_description)->get()->pluck('uom_code');
      return $list;
    }


    private function save_costing_revision($id, $revision_no, $revision_reason){

      $costing = (array)DB::table('costing')->where('id', '=', $id)->first();
      $costing = json_decode( json_encode($costing), true);//convert resullset to array
      $costing['revision_reason'] = $revision_reason;
      DB::table('costing_history')->insert($costing);

      $finish_goods = DB::table('costing_finish_goods')->where('costing_id', '=', $id)->get();
      $finish_goods = json_decode( json_encode($finish_goods), true);//convert resullset to array
      $fg_id_arr = [];
      for($x = 0 ; $x < sizeof($finish_goods); $x++){
        $finish_goods[$x]['revision_no'] = $revision_no;
        array_push($fg_id_arr, $finish_goods[$x]['fg_id']);
      }
      DB::table('costing_finish_goods_history')->insert($finish_goods);

      $fg_components = DB::table('costing_finish_good_components')->whereIn('fg_id', $fg_id_arr)->get();
      $fg_components = json_decode( json_encode($fg_components), true);//convert resullset to array
      $fg_components_id_arr = [];
      for($x = 0 ; $x < sizeof($fg_components); $x++){
        $fg_components[$x]['revision_no'] = $revision_no;
        array_push($fg_components_id_arr, $fg_components[$x]['id']);
      }
      DB::table('costing_finish_good_components_history')->insert($fg_components);

      $fg_component_items = DB::table('costing_finish_good_component_items')->whereIn('fg_component_id', $fg_components_id_arr)->get();
      $fg_component_items = json_decode( json_encode($fg_component_items), true);//convert resullset to array
      for($x = 0 ; $x < sizeof($fg_component_items); $x++){
        $fg_component_items[$x]['revision_no'] = $revision_no;
      }
      DB::table('costing_finish_good_component_items_history')->insert($fg_component_items);

    }

}
