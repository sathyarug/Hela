<?php

namespace App\Http\Controllers\Merchandising\Costing;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use App\Models\Merchandising\Costing\Costing;
use App\Models\Merchandising\Costing\CostingSizeChart;
use App\Models\Merchandising\Costing\CostingFngColor;
use App\Models\Merchandising\Costing\CostingSfgColor;
use App\Models\Merchandising\Costing\CostingCountry;
use App\Models\Merchandising\Costing\CostingItem;

use App\Models\Org\SizeChart;
//use App\Models\Merchandising\Costing\BulkCostingApproval;
//use App\Models\MerchandisBulkCostingDetailsing\Costing\BulkCostingDetails;
//use App\Models\Merchandising\Costing\BulkCostingFeatureDetails;
//use App\Models\Merchandising\Costing\CostingBulkRevision;
//use App\Models\Merchandising\Costing\CostingFinishGood;
//use App\Models\Merchandising\Costing\CostingFinishGoodComponent;
use App\Models\Finance\Cost\FinanceCost;
use App\Models\Merchandising\StyleCreation;
use App\Models\Merchandising\ProductFeature;
use App\Models\Merchandising\ProductFeatureComponent;
//use App\Models\Merchandising\Costing\CostingFinishGoodComponentItem;
use App\Models\Org\UOM;
use App\Models\Org\Color;
use App\Models\Merchandising\CustomerOrderDetails;
use App\Models\Merchandising\Item\Item;
use App\Models\Merchandising\Item\Category;
use App\Models\Merchandising\Item\SubCategory;
use App\Models\Merchandising\ProductComponent;
use App\Models\Merchandising\ProductSilhouette;
use App\Models\Org\Division;
use App\Models\Org\Season;
use App\Models\Merchandising\Costing\CostingFngItem;
use App\Models\Merchandising\Costing\CostingSfgItem;

use App\Models\Merchandising\BOMHeader;
use App\Models\Merchandising\BOMDetails;

use App\Libraries\Approval;

class CostingController extends Controller {


    public function index(Request $request) {
        $type = $request->type;
        if ($type == 'getStyleData') {
            return response($this->getStyleData($request->style_id));
        }
        elseif ($type == 'getColorForDivision'){
            $division_id = $request->division_id;
            $query = $request->query;
            return response($this->getColorForDivision($division_id,$request->get('query')));
        }
        /*elseif ($type == 'getFinishGood') {
            return response($this->getFinishGood($request->id));
        }*/
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
        else if($type == 'auto') {
          $search = $request->search;
          return response($this->autocomplete_search($search));
        }
        else if($type == 'item_from_article_no') {
          $article_no = $request->article_no;
          return response([
            'data' => $this->get_item_from_article_no($article_no)
          ]);
        }
        else if($type == 'get_finish_good_color'){
            return response([
              'data' => $this->get_finish_good_color($request->style_id)
            ]);
        }
        else if($type == 'costing_colors'){
            $costing_id = $request->costing_id;
            return response([
              'data' => $this->get_saved_finish_good_colors($costing_id)
            ]);
        }
        else if($type == 'get_product_feature_components'){
          $style_id = $request->style_id;
          return response([
            'data' => $this->get_product_feature_components($style_id)
          ]);
        }
        else if($type == 'get_saved_size_chart'){
          $costing_id = $request->costing_id;
          return response([
            'data' => $this->get_saved_size_chart($costing_id)
          ]);
        }
        else if($type == 'get_saved_countries'){
          $costing_id = $request->costing_id;
          return response([
            'data' => $this->get_saved_countries($costing_id)
          ]);
        }
        else if($type == 'costing_finish_goods'){
          $costing_id = $request->costing_id;
          return response([
            'data' => $this->get_costing_finish_goods($costing_id)
          ]);
        }
        else if($type == 'costing_smv_details'){
          $costing_id = $request->costing_id;
          return response([
            'data' => $this->get_costing_smv_details($costing_id)
          ]);
        }
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
              $costing->fill($request->except(['upcharge_reason_description', 'division', 'style_description', 'style_remarks', 'customer', 'status']));
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
                 $cpm_factory = ($cpum * $costing->planned_efficiency) / 100;
                 $cpm_factory = round($cpm_factory, 4, PHP_ROUND_HALF_UP);
              }

              $total_smv = $this->get_total_smv($costing->style_id, $costing->bom_stage_id, $costing->color_type_id);//get smv details
              $labour_cost = round(($total_smv * $cpm_factory), 4, PHP_ROUND_HALF_UP);//calculate labour cost
              $coperate_cost = round(($total_smv * $cpm_front_end), 4, PHP_ROUND_HALF_UP);//calculate coperate cost

              $costing->finance_charges = $finance_charges;
              $costing->finance_cost = 0; //finance cost = total rm cost * finance charges
              $costing->fabric_cost = 0;
              $costing->elastic_cost = 0;
              $costing->trim_cost = 0;
              $costing->packing_cost = 0;
              $costing->other_cost = 0;
              $costing->cpm_front_end = $cpm_front_end;
              $costing->cost_per_utilised_min = $cpum;
              $costing->total_smv = $total_smv;
              $costing->cpm_factory = $cpm_factory;
              $costing->labour_cost = $labour_cost;
              $costing->coperate_cost = $coperate_cost;
              $costing->revision_no = 0;
              $costing->status = 'CREATE';
              $costing->save();

              $costing->sc_no = str_pad($costing->id, 5, '0', STR_PAD_LEFT);
              $costing->save();//generate sc no and update

              //get product feature components from style
              //$finish_goods = $this->get_finish_good($costing->style_id, $costing->bom_stage_id, $costing->color_type_id);
              $costing = Costing::with(['style'])->find($costing->id);
              $feature_component_count = ProductFeature::find($costing->style->product_feature_id)->count;
              //send response
              return response(['data' => [
                  'status' => 'success',
                  'message' => 'Costing saved successfully',
                  'costing' => $costing,
                  'feature_component_count' => $feature_component_count,
                  //'finish_goods' => $finish_goods
                ]
              ], Response::HTTP_CREATED);
          } else {
              $errors = $costing->errors(); // failure, get errors
              return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
          }
        }
    }


    public function show($id) {

       $costing = Costing::with(['style', 'bom_stage', 'season', 'color_type','upcharge_reason','buy','design_source','pack_type'])->find($id);
       //$finish_goods_count = CostingFinishGood::where('costing_id', '=', $costing->id)->count();
       //$finish_goods = [];

       /*if($finish_goods_count > 0){ //chek already saved finishgood
         $finish_goods = $this->get_saved_finish_good($id); //get saved finish good details
       }
       else{
         $finish_goods = $this->get_finish_good($costing->style_id, $costing->bom_stage_id, $costing->color_type_id); //get finishgood data from smv table
       }*/

      $feature_component_count = ProductFeature::find($costing->style->product_feature_id)->count;

      return response([
        'data' => [
          'costing' => $costing,
          //'finish_goods' => $finish_goods,
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
            'message' => 'Cannot update '. $costing->status .' costing'
          ]
        ]);
      }
      else {
        $style = StyleCreation::find($costing->style_id);
        $product_feature = ProductFeature::find($style->product_feature_id);
        //$finish_goods = $request->finish_goods;//get finisg goods list

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
        $costing->style_type = $request->style_type;

        $cpm_factory = ($costing->cost_per_utilised_min * $costing->planned_efficiency) / 100;
        $cpm_factory = round($cpm_factory, 4, PHP_ROUND_HALF_UP );
        $labour_cost = round(($costing->total_smv * $cpm_factory), 4, PHP_ROUND_HALF_UP);
        // no need to update corperate cost. Because it will not change based on user inut data
        $costing->cpm_factory = $cpm_factory;
        $costing->labour_cost = $labour_cost;
        $costing->save();

        /*$current_pack = -1;
        for($x = 0 ; $x < sizeof($finish_goods) ; ($x = $x + $product_feature->count)){
            $fg = null;
            if($finish_goods[$x]['fg_id'] == 0){ //new finish good
              $fg = new CostingFinishGood();
              $fg->costing_id = $costing->id;
              $fg->epm = 0;
              $fg->np = 0;
            }
            else{
              $fg = CostingFinishGood::find($finish_goods[$x]['fg_id']);// get existing finish good
              $fg->epm = $fg->calculate_epm($costing->fob, $fg->total_rm_cost, $costing->total_smv);//calculate finish good epm
              $fg->np = $fg->calculate_np($costing->fob, $fg->total_cost);//calculate np value
            }

            if($product_feature->count == 1) {//single pack, then set combo color to garment color
              $fg->combo_color_id = $this->get_color_id_from_name($finish_goods[$x]['color']);
            }
            else {// multiple pack
              $fg->combo_color_id = $this->get_color_id_from_name($finish_goods[$x]['combo_color']);
            }

            $fg->pack_no = $finish_goods[$x]['pack_no'];
            $fg->pack_no_code = 'FG'.str_pad($fg->pack_no, 3, '0', STR_PAD_LEFT);
            $fg->product_feature = $finish_goods[$x]['product_feature_id'];
            $fg->save();

            //set and save costing header with first finisg good's epm and np margine
            if($x == 0){//update costing epm and np margine
              $costing->epm = $fg->epm;
              $costing->np_margine = $fg->np;
              $costing->save();
            }

            $fg->fg_code = 'FNG'.str_pad($fg->fg_id, 7, '0', STR_PAD_LEFT);
            $fg->save();//generate and save finish good code

            for($y = $x ; $y < ($x + $product_feature->count) ; $y++) {
              if($finish_goods[$y]['id'] == 0){ //new component
                $finish_good_component = new CostingFinishGoodComponent();
                $finish_good_component->fg_id = $fg->fg_id;
              }
              else{
                $finish_good_component = CostingFinishGoodComponent::find($finish_goods[$y]['id']);
              }

              $finish_good_component->color_id = $this->get_color_id_from_name($finish_goods[$y]['color']);
              $finish_good_component->product_component_id = $finish_goods[$y]['product_component_id'];
              $finish_good_component->product_silhouette_id = $finish_goods[$y]['product_silhouette_id'];
              $finish_good_component->line_no = $finish_goods[$y]['line_no'];
              $finish_good_component->surcharge = $finish_goods[$y]['surcharge'];
              $finish_good_component->mcq = $finish_goods[$y]['mcq'];
              $finish_good_component->smv = $finish_goods[$y]['smv'];
              $finish_good_component->status = 1;
              $finish_good_component->save();

              $finish_good_component->sfg_code = 'SFG'.str_pad($finish_good_component->id, 7, '0', STR_PAD_LEFT);
              $finish_good_component->save();//generate and save finish good code
            }
          }*/

          return response([
            'data' => [
              'status' => 'success',
              'message' => 'Costing updated successfully',
              'costing' => $costing,
              //'finish_goods' => $this->get_saved_finish_good($costing->id),
              'feature_component_count' => $product_feature->count,
            ]
          ]);
      }
    }


    public function destroy($id) {

    }


  /*  public function approve_costing(Request $request) {
      $costing_id = $request->costing_id;
      $costing = Costing::find($costing_id);
      if($costing->status != 'APPROVED'){
        $costing->status = 'APPROVED';
        $costing->save();
        $this->generate_bom_for_costing($costing_id);//generate boms for all coonected deliveries
      }
    }*/


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


    /*private function getFinishGood($id) {
      return [
        'finish_goods' => $this->get_saved_finish_good($id)
      ];
    }*/

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
        //check all finish goods have connected sales order deliveries
        $costing = Costing::find($request->costing_id);
        $user = auth()->user();

        $fg_colors_count = CostingFngColor::where('costing_id', '=', $costing->id)->count();

        if($fg_colors_count > 0) {//has finish good colors
          //check has countries
          $country_count = CostingCountry::where('costing_id', '=', $costing->id)->count();
          if($country_count > 0){//check has countries
            //check for items
            $item_count = CostingItem::where('costing_id', '=', $costing->id)->count();
            if($item_count > 0){//has item
              $costing->status = 'PENDING';
              $costing->approval_user = null;
              $costing->approval_sent_user = $user->user_id;
              $costing->approval_sent_date = date("Y-m-d H:i:s");
              $costing->save();

              $approval = new Approval();
              $approval->start('COSTING', $costing->id, $costing->created_by);//start costing approval process

              return response([
                'data' => [
                  'status' => 'success',
                  'message' => 'Costing sent for approval',
                  'costing' => $costing
                ]
              ]);
            }
            else {//no item
              return response([
                'data' => [
                  'status' => 'error',
                  'message' => 'Cannot approve costing. There is no items.',
                  'costing' => $costing
                ]
              ]);
            }
          }
          else {
            return response([
              'data' => [
                'status' => 'error',
                'message' => 'Cannot approve costing. There is no delivery countries',
                'costing' => $costing
              ]
            ]);
          }
        }
        else { //no finish goods
          return response([
            'data' => [
              'status' => 'error',
              'message' => 'Cannot approve costing. There is no colors.',
              'costing' => $costing
            ]
          ]);
        }
    }


    public  function getColorForDivision($division_id,$query){
      //$color=\App\Models\Org\Color::where([['division_id','=',$division_id]])->pluck('color_name')->toArray();
        $color=\App\Models\Org\Color::where('status', '=', 1)->where('color_code', 'like', $query.'%')->pluck('color_code')->toArray();
        return json_encode($color);
    }




    /*public function copy_finish_good(Request $request){
      $fg_id = $request->fg_id;
      $proceed_without_warning = ($request->proceed_without_warning == null) ? false : $request->proceed_without_warning;
      //count finish good items
      $components_item_count = CostingFinishGoodComponent::select("costing_finish_good_components.id",
        DB::raw("(SELECT count(costing_finish_good_component_items.id) FROM costing_finish_good_component_items
        WHERE costing_finish_good_component_items.fg_component_id = costing_finish_good_components.id) as item_count")
      )->where('costing_finish_good_components.fg_id', '=', $fg_id)->get();

      $has_error = 0;
      for($x = 0 ; $x < sizeof($components_item_count) ; $x++){
        if($components_item_count[$x]->item_count <= 0){
          $has_error++;
        }
      }

      if($has_error >= sizeof($components_item_count)){ //no item for every component
        return response([
          'data' => [
            'status' => 'error',
            'message' => 'You must add items before copy finish good'
          ]
        ]);
      }
      else if($has_error > 0 && $has_error < sizeof($components_item_count) && $proceed_without_warning == false){//some components don't have items and need show warring
        return response([
          'data' => [
            'status' => 'warning',
            'message' => "Some components don't have items."
          ]
        ]);
      }*/
      /*$fg_item_count = CostingFinishGoodComponentItem::join('costing_finish_good_components', 'costing_finish_good_components.id', '=', 'costing_finish_good_component_items.fg_component_id')
      ->where('costing_finish_good_components.fg_id', '=', $fg_id)
      ->count();*/
      //if($fg_item_count == null || $fg_item_count <= 0) {
      /*else {
        $finish_good = CostingFinishGood::find($fg_id);
        $finish_good_copy = $finish_good->replicate();
        $finish_good_copy->pack_no = DB::table('costing_finish_goods')->where('costing_id', '=', $finish_good->costing_id)->max('pack_no') + 1;
        $finish_good_copy->pack_no_code = 'FG'.str_pad($finish_good_copy->pack_no, 3, '0', STR_PAD_LEFT);
        $finish_good_copy->combo_color_id = null;
        $finish_good_copy->save();

        $components = CostingFinishGoodComponent::where('fg_id', '=', $finish_good->fg_id)->get();
        for($x = 0 ; $x < sizeof($components) ; $x++){
          $component_copy = $components[$x]->replicate();
          $component_copy->fg_id = $finish_good_copy->fg_id;
          $component_copy->color_id = null;
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
    }*/


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
      else {

        $new_total_smv = $this->get_total_smv($costing->style_id, $bom_stage_id, $color_type_id);
        if($new_total_smv <= 0) { //check total smv for new costing, if smv == 0, then send error message
          return response(['data' => [
              'status' => 'error',
              'message' => 'No SMV details avaliable for selected costing combination.'
            ]
          ]);
        }
        else {
          $costing_copy = $costing->replicate();
          $costing_copy->bom_stage_id = $bom_stage_id;
          $costing_copy->season_id = $season_id;
          $costing_copy->color_type_id = $color_type_id;
          $costing_copy->sc_no = null;
          $costing_copy->approval_user = null;
          $costing_copy->approval_date = null;
          $costing_copy->approval_sent_date = null;
          $costing_copy->approval_sent_user = null;
          $costing_copy->total_smv = $new_total_smv;
          $costing_copy->status = 'CREATE';
          $costing_copy->save();

          $costing_copy->sc_no = str_pad($costing_copy->id, 5, '0', STR_PAD_LEFT);
          $costing_copy->save();//generate sc no and update

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
              'message' => 'Costing copied successfully'
            ]
          ]);
        }
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
          'message' => 'Finish good deleted successfully.',
          'feature_component_count' => sizeof($components),
          'finish_goods' => $this->get_saved_finish_good($finish_good->costing_id)
        ]
      ] , Response::HTTP_OK);
    }


    //************************* size chart section *****************************

    public function update_size_chart(Request $request){
      $costing_id = $request->costing_id;
      $size_chart_id = $request->size_chart_id;
      $sizes = $request->sizes;

      $costing = Costing::find($costing_id);
      $costing->size_chart_id = $size_chart_id;
      $costing->save();

      CostingSizeChart::where('costing_id', '=', $costing_id)->delete();
      $data = [];
      for($x = 0 ; $x < sizeof($sizes) ; $x++){
        array_push($data, [
          'costing_id' => $costing_id,
          'size_chart_id' => $size_chart_id,
          'size_id' => $sizes[$x]['size_id'],
          'status' => $sizes[$x]['status']
        ]);
      }
      CostingSizeChart::insert($data);

      return response([
        'data' => [
          'status' => 'success',
          'message' => 'Size chart updated successfully.'
        ]
      ]);
    }


    public function get_saved_size_chart($costing_id){
      $costing = Costing::find($costing_id);

      if($costing->size_chart_id != null){
        $size_chart = SizeChart::find($costing->size_chart_id);
        $sizes = DB::select("SELECT
          org_size_chart_sizes.size_chart_id,
          org_size_chart_sizes.size_id,
          org_size.size_name,
          IF
          (
          	(
          		SELECT status FROM costing_size_chart WHERE costing_size_chart.costing_id = ? AND
          		costing_size_chart.size_chart_id = org_size_chart_sizes.size_chart_id AND costing_size_chart.size_id = org_size_chart_sizes.size_id
          	) = 1, 1, 0
          ) AS status
          FROM org_size_chart_sizes
          INNER JOIN org_size ON org_size.size_id = org_size_chart_sizes.size_id
          WHERE org_size_chart_sizes.size_chart_id = ?", [$costing_id, $costing->size_chart_id]);

          return [
            'size_chart' => $size_chart,
            'sizes' => $sizes
          ];

      }
      else {
        return [
          'size_chart' => null
        ];
      }
    }

    //************************** Costing Colors ********************************

    public function save_costing_colors(Request $request){
      $colors = $request->colors;
      $costing_id = $request->costing_id;
      $costing = Costing::find($costing_id);
      $style = StyleCreation::find($costing->style_id);
      $product_feature = ProductFeature::find($style->product_feature_id);

      for($x = 0 ; $x < sizeof($colors) ; ($x = $x + $product_feature->count)){

        $fng_color = null;
        if($colors[$x]['fng_color_id'] == 0){ //new fng color
          $fng_color = new CostingFngColor();
        }
        else {
          $fng_color = CostingFngColor::find($colors[$x]['fng_color_id']);
        }

        $fng_color->costing_id = $costing->id;
        $fng_color->color_id = $colors[$x]['fng_color'];
        $fng_color->product_feature_id = $colors[$x]['product_feature_id'];
        $fng_color->save();

        for($y = $x ; $y < ($x + $product_feature->count) ; $y++) {
          $sfg_color = null;
          if($colors[$y]['sfg_color_id'] == 0){ //new sfg color
            $sfg_color = new CostingSfgColor();
          }
          else {
            $sfg_color = CostingSfgColor::find($colors[$y]['sfg_color_id']);
          }
          ;
          $sfg_color->fng_color_id = $fng_color->fng_color_id;
          $sfg_color->color_id = $colors[$y]['sfg_color'];
          $sfg_color->product_component_id = $colors[$y]['product_component_id'];
          $sfg_color->product_silhouette_id = $colors[$y]['product_silhouette_id'];
          $sfg_color->save();
        }
      }

      return response(['data' => [
        'status' => 'success',
        'message' => 'Colors saved successfully',
        'colors' => $this->get_saved_finish_good_colors($costing->id),
        'component_count' => $product_feature->count
      ]]);
    }


    public function remove_costing_color(Request $request){
      $fng_color_id = $request->fng_color_id;

      CostingSfgColor::where('fng_color_id', '=', $fng_color_id)->delete();
      $fng_color = CostingFngColor::find($fng_color_id);
      $fng_color->delete();

      return response(['data' => [
        'status' => 'success',
        'message' => 'Color removed successfully',
        'colors' => $this->get_saved_finish_good_colors($fng_color->costing_id),
      ]]);
    }



    //********************** Costing Countries *********************************

    public function save_costing_countries(Request $request){
      $countries = $request->countries;
      $costing_id = $request->costing_id;
      $costing = Costing::find($costing_id);

      for($x = 0 ; $x < sizeof($countries) ; $x++){

        $country = null;
        if($countries[$x]['costing_country_id'] == 0){ //new fng color
          $country = new CostingCountry();
        }
        else {
          $country = CostingCountry::find($countries[$x]['costing_country_id']);
        }

        $country->costing_id = $costing->id;
        $country->country_id = $countries[$x]['country_id'];
        $country->fob = $countries[$x]['fob'];
        $country->save();

      }

      return response(['data' => [
        'status' => 'success',
        'message' => 'Country saved successfully',
        'countries' => $this->get_saved_countries($costing->id)
      ]]);
    }


    public function remove_costing_country(Request $request){
      $costing_country_id = $request->costing_country_id;
      $costing_country = CostingCountry::find($costing_country_id);
      $costing_country->delete();

      return response(['data' => [
        'status' => 'success',
        'message' => 'Country removed successfully',
        'countries' => $this->get_saved_countries($costing_country->costing_id),
      ]]);
    }


    private function get_saved_countries($costing_id){
      $list = DB::select("SELECT
        costing_country.*,
        org_country.country_description,
        org_country.country_code
        FROM costing_country
        INNER JOIN org_country ON org_country.country_id = costing_country.country_id
        WHERE costing_country.costing_id = ?", [$costing_id]);

        return $list;
    }

    //*********************** Costing finish goods and bom *********************

    public function genarate_bom(Request $request){
      $costing_id = $request->costing_id;

      $costing = Costing::with(['style','buy'])->find($costing_id);
      $fng_colors = CostingFngColor::where('costing_id', '=', $costing_id)->get();
      $countries = $this->get_saved_countries($costing_id);

      $category = Category::where('category_code', '=', 'FNG')->first();
      $sfg_category = Category::where('category_code', '=', 'SFG')->first();

      $product_silhouette = ProductSilhouette::find($costing->style->product_silhouette_id);
      $division = Division::find($costing->style->division_id);
    //echo json_encode($division);die();
      $season = Season::find($costing->season_id);
      $uom = UOM::where('uom_code', '=', 'pcs')->first();

      //echo json_encode($costing_items);die();
      //finish good
      //style_code . silhoutte . division . color_code . season . country . buy name
      foreach ($fng_colors as $fng_color) {

        //$sfg_colors = CostingSfgColor::with(['product_component', 'product_silhouette'])->where('fng_color_id', '=', $fng_color->fng_color_id)->get();
        $sfg_colors = CostingSfgColor::where('fng_color_id', '=', $fng_color->fng_color_id)->get();

        foreach($countries as $country){
          $item = new Item();
          $description = $costing->style->style_no.'_'.$product_silhouette->product_silhouette_description.'_'
            .$division->division_code.'_'.$fng_color->color->color_code.'_'.$season->season_code.'_'.$country->country_code.
            '_'.$costing->buy->buy_name;

          $item_count = Item::where('master_description', '=', $description)->count();
          if($item_count > 0){
            continue;
          }

          $item->category_id = $category->category_id;
          $item->subcategory_id = 0;
          $item->master_description = $description;
          $item->parent_item_id = null;
          $item->inventory_uom = $uom->uom_id;
          $item->standard_price = null;
          $item->supplier_id = null;
          $item->supplier_reference = null;
          $item->color_wise = 1;
          $item->size_wise = null;
          $item->color_id = $fng_color->color_id;
          $item->status = 1;
          $item->save();
          //generate item codes
          $item->master_code = $category->category_code . str_pad($item->master_id, 7, '0', STR_PAD_LEFT);
          $item->save();

          $fng_item = new CostingFngItem();
          $fng_item->costing_id = $costing_id;
          $fng_item->fng_id = $item->master_id;
          $fng_item->country_id = $country->country_id;
          $fng_item->fng_color_id = $fng_color->color_id;
          $fng_item->save();

          $bom_header = new BOMHeader();
          $bom_header->costing_id = $costing_id;
          $bom_header->fng_id = $item->master_id;
          $bom_header->epm = 0;
          $bom_header->np_margin = 0;
          $bom_header->total_rm_cost = 0;
          $bom_header->finance_cost = 0;
          $bom_header->total_cost = 0;
          $bom_header->country_id = $country->country_id;
          $bom_header->status = 'RELEASED';
          $bom_header->save();

          //generate sfg items
          foreach ($sfg_colors as $sfg_color) {
              $item2 = new Item();
              $silhouette = ProductSilhouette::find($sfg_color->product_silhouette_id);
              $component = ProductComponent::find($sfg_color->product_component_id);
          //echo json_encode($sfg_color->product_component_id);die();
              $description = $costing->style->style_no.'_'.$product_silhouette->product_silhouette_description.'_'
                .$division->division_code.'_'.$fng_color->color->color_code.'_'.$season->season_code.'_'.$country->country_code.
                '_'.$costing->buy->buy_name.'_'.$component->product_component_description.'_'.
                $silhouette->product_silhouette_description;

              $sfg_item_count = Item::where('master_description', '=', $description)->count();
              if($sfg_item_count > 0){
                continue;
              }

              $item2->category_id = $sfg_category->category_id;
              $item2->subcategory_id = 0;
              $item2->master_description = $description;
              $item2->parent_item_id = null;
              $item2->inventory_uom = $uom->uom_id;
              $item2->standard_price = null;
              $item2->supplier_id = null;
              $item2->supplier_reference = null;
              $item2->color_wise = 1;
              $item2->size_wise = null;
              $item2->color_id = $sfg_color->color_id;
              $item2->status = 1;
              $item2->save();
              //generate item codes
              $item2->master_code = $sfg_category->category_code . str_pad($item2->master_id, 7, '0', STR_PAD_LEFT);
              $item2->save();

              $sfg_item = new CostingSfgItem();
              $sfg_item->costing_id = $costing_id;
              $sfg_item->sfg_id = $item2->master_id;
              $sfg_item->country_id = $country->country_id;
              $sfg_item->sfg_color_id = $sfg_color->color_id;
              $sfg_item->costing_fng_id = $fng_item->costing_fng_id;
              $sfg_item->product_component_id = $sfg_color->product_component_id;
              $sfg_item->product_silhouette_id = $sfg_color->product_silhouette_id;
              $sfg_item->save();

              $costing_items = CostingItem::where('costing_id', '=', $costing_id)
              ->where('product_component_id', '=', $sfg_item->product_component_id)
              ->where('product_silhouette_id', '=', $sfg_item->product_silhouette_id)->get();
              //create bom items
              foreach($costing_items as $costing_item) {
                $bom_detail = new BOMDetails();
                $bom_detail->bom_id = $bom_header->bom_id;
                $bom_detail->costing_item_id = $costing_item->costing_item_id;
                $bom_detail->costing_id = $costing_id;
                $bom_detail->feature_component_id = $costing_item->feature_component_id;
                $bom_detail->product_component_id = $costing_item->product_component_id;
                $bom_detail->product_silhouette_id = $costing_item->product_silhouette_id;
                $bom_detail->inventory_part_id = $costing_item->inventory_part_id;
                $bom_detail->position_id = $costing_item->position_id;
                $bom_detail->purchase_uom_id = $costing_item->purchase_uom_id;
                $bom_detail->supplier_id = null;
                $bom_detail->origin_type_id = $costing_item->origin_type_id;
                $bom_detail->garment_options_id = $costing_item->garment_options_id;
                $bom_detail->purchase_price = $costing_item->unit_price;
                $bom_detail->bom_unit_price = $costing_item->unit_price;
                $bom_detail->net_consumption = $costing_item->net_consumption;
                $bom_detail->wastage = $costing_item->wastage;
                $bom_detail->gross_consumption = $costing_item->gross_consumption;
                $bom_detail->meterial_type = $costing_item->meterial_type;
                $bom_detail->freight_charges = $costing_item->freight_charges;
                $bom_detail->mcq = $costing_item->mcq;
                $bom_detail->surcharge = $costing_item->surcharge;
                $bom_detail->total_cost = $costing_item->total_cost;
                $bom_detail->ship_mode = $costing_item->ship_mode;
                $bom_detail->ship_term_id = $costing_item->ship_term_id;
                $bom_detail->lead_time = $costing_item->lead_time;
                $bom_detail->country_id = $costing_item->country_id;
                $bom_detail->comments = $costing_item->comments;
                $bom_detail->status = 1;
                $bom_detail->save();
              }
          }
        }
        //sfg item generation
      }
      return response([
        'data' => [
          'status' => 'success',
          'message' => 'Bom generated successfully.'
        ]
      ]);
    }


    private function get_costing_finish_goods($costing_id){
      $list = DB::select("SELECT
          costing_sfg_item.costing_id,
          costing_fng_item.costing_fng_id,
          costing_fng_item.fng_id,
          item_master_fng.master_code AS fng_code,
          item_master_fng.master_description AS fng_description,
          org_color_fng.color_code AS fng_color_code,
          org_color_fng.color_name AS fng_color_name,
          costing_sfg_item.costing_sfg_id,
          costing_sfg_item.sfg_id,
          item_master_sfg.master_code AS sfg_code,
          item_master_sfg.master_description AS sfg_description,
          org_color_sfg.color_code AS sfg_color_code,
          org_color_sfg.color_name AS sfg_color_name,
          org_country.country_description
          FROM
          costing_sfg_item
          INNER JOIN costing_fng_item ON costing_fng_item.costing_fng_id = costing_sfg_item.costing_fng_id
          INNER JOIN item_master AS item_master_sfg ON item_master_sfg.master_id = costing_sfg_item.sfg_id
          INNER JOIN item_master AS item_master_fng ON item_master_fng.master_id = costing_fng_item.fng_id
          INNER JOIN org_country ON org_country.country_id = costing_sfg_item.country_id
          INNER JOIN org_color AS org_color_fng ON org_color_fng.color_id = costing_fng_item.fng_color_id
          INNEr JOIN org_color AS org_color_sfg ON org_color_sfg.color_id = costing_sfg_item.sfg_color_id
          WHERE costing_sfg_item.costing_id = ?", [$costing_id]);
          return $list;
    }

    //*************** Costing SMV details **************************************

    private function get_costing_smv_details($costing_id){
      $list = DB::select("SELECT
        ie_component_smv_details.details_id,
        ie_component_smv_details.product_component_id,
        product_component.product_component_description,
        ie_component_smv_details.product_silhouette_id,
        product_silhouette.product_silhouette_description,
        ie_garment_operation_master.garment_operation_name,
        ie_component_smv_details.smv
        FROM
        ie_component_smv_details
        INNER JOIN ie_component_smv_header ON ie_component_smv_header.smv_component_header_id = ie_component_smv_details.smv_component_header_id
        INNER JOIN product_component ON product_component.product_component_id = ie_component_smv_details.product_component_id
        INNER JOIN product_silhouette ON product_silhouette.product_silhouette_id = ie_component_smv_details.product_silhouette_id
        INNER JOIN ie_garment_operation_master ON ie_garment_operation_master.garment_operation_id = ie_component_smv_details.garment_operation_id
        INNER JOIN costing ON costing.style_id = ie_component_smv_header.style_id
        	AND costing.bom_stage_id = ie_component_smv_header.bom_stage_id
        	AND costing.color_type_id = ie_component_smv_header.col_opt_id
        WHERE costing.id = ?", [$costing_id]);
      return $list;
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


    /*private function get_finish_good($style_id, $bom_stage, $color_type) {
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
        'FG001' AS pack_no_code,
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
    }*/

    private function get_finish_good_color($style_id) {
      $product_feature_components = DB::select("SELECT
        product_feature.product_feature_id,
        product_component.product_component_description,
        product_silhouette.product_silhouette_description,
        product_feature.product_feature_description,
        product_component.product_component_id,
        product_silhouette.product_silhouette_id,
        product_feature.product_feature_id,
        0 AS fng_color,
        0 AS sfg_color,
        '' AS fng_color_code,
        '' AS fng_color_name,
        '' AS sfg_color_code,
        '' AS sfg_color_name,
        0 AS fng_color_id,
        0 AS sfg_color_id,
        1 AS edited
        FROM product_feature_component
				INNER JOIN product_feature ON product_feature.product_feature_id = product_feature_component.product_feature_id
        INNER JOIN product_silhouette ON product_silhouette.product_silhouette_id = product_feature_component.product_silhouette_id
        INNER JOIN product_component ON product_component.product_component_id = product_feature_component.product_component_id
        INNER JOIN style_creation ON style_creation.product_feature_id = product_feature.product_feature_id
        WHERE style_creation.style_id = ?", [$style_id]);

        return $product_feature_components;
    }


    private function get_saved_finish_good_colors($costing_id) {

      $list = DB::select("SELECT
        product_component.product_component_description,
        product_silhouette.product_silhouette_description,
        product_feature.product_feature_description,
        product_feature.product_feature_id,
        product_component.product_component_id,
        product_silhouette.product_silhouette_id,
        org_color_fng.color_id AS fng_color,
        org_color_fng.color_code AS fng_color_code,
        org_color_fng.color_name AS fng_color_name,
        org_color_sfg.color_id AS sfg_color,
        org_color_sfg.color_code AS sfg_color_code,
        org_color_sfg.color_name AS sfg_color_name,
        costing_fng_color.fng_color_id,
        costing_sfg_color.sfg_color_id,
        0 AS edited
        FROM
        costing_sfg_color
        INNER JOIN costing_fng_color ON costing_fng_color.fng_color_id = costing_sfg_color.fng_color_id
        INNER JOIN costing ON costing.id = costing_fng_color.costing_id
        INNER JOIN product_feature ON product_feature.product_feature_id = costing_fng_color.product_feature_id
        INNER JOIN product_silhouette ON product_silhouette.product_silhouette_id = costing_sfg_color.product_silhouette_id
        INNER JOIN product_component ON product_component.product_component_id = costing_sfg_color.product_component_id
        INNER JOIN org_color AS org_color_fng ON org_color_fng.color_id = costing_fng_color.color_id
        INNER JOIN org_color AS org_color_sfg ON org_color_sfg.color_id = costing_sfg_color.color_id
        WHERE costing.id = ?", [$costing_id]);

        return $list;
    }


    /*private function get_saved_finish_good($id){
      $costing = Costing::find($id);
      //$style = StyleCreation::find($costing->style_id);

      $product_feature_components = DB::select("SELECT
        costing_finish_good_components.*,
        costing_finish_goods.pack_no,
        costing_finish_goods.pack_no_code,
        costing_finish_goods.combo_color_id,
        costing_finish_goods.epm,
        costing_finish_goods.np,
        product_feature.product_feature_id,
        product_component.product_component_description,
        product_silhouette.product_silhouette_description,
        product_feature.product_feature_description,
        color1.color_code AS combo_color,
        color1.color_code AS combo_color2,
        color2.color_code AS color
        FROM costing_finish_good_components
        INNER JOIN costing_finish_goods ON costing_finish_goods.fg_id = costing_finish_good_components.fg_id
        INNER JOIN product_component ON product_component.product_component_id = costing_finish_good_components.product_component_id
        INNER JOIN product_silhouette ON product_silhouette.product_silhouette_id = costing_finish_good_components.product_silhouette_id
        INNER JOIN product_feature ON product_feature.product_feature_id = costing_finish_goods.product_feature
        LEFT JOIN org_color AS color1 ON color1.color_id = costing_finish_goods.combo_color_id
        LEFT JOIN org_color AS color2 ON color2.color_id = costing_finish_good_components.color_id
        WHERE costing_finish_goods.costing_id = ?
        ORDER BY costing_finish_good_components.fg_id, costing_finish_good_components.id", [$id]);

        return $product_feature_components;
    }*/


    private function datatable_search($data)
    {
          $start = $data['start'];
          $length = $data['length'];
          $draw = $data['draw'];
          $search = $data['search']['value'];
          $order = $data['order'][0];
          $order_column = $data['columns'][$order['column']]['data'];
          $order_type = $order['dir'];
          $user_id = auth()->user()->user_id;
//echo $user_id;die();
          $costing_list = Costing::select('costing.*','style_creation.style_no','merc_bom_stage.bom_stage_description',
            'org_season.season_name', 'merc_color_options.color_option')
          ->join('style_creation', 'style_creation.style_id', '=', 'costing.style_id')
          ->join('merc_bom_stage', 'merc_bom_stage.bom_stage_id', '=', 'costing.bom_stage_id')
          ->join('org_season', 'org_season.season_id', '=', 'costing.season_id')
          ->join('merc_color_options', 'merc_color_options.col_opt_id', '=', 'costing.color_type_id')
          ->where('costing.created_by', '=', $user_id)
          ->where(function ($query) use ($search) {
              $query->orWhere('costing.id', 'like', $search.'%' )
              ->orWhere('style_creation.style_no'  , 'like', $search.'%' )
              ->orWhere('merc_bom_stage.bom_stage_description','like',$search.'%')
              ->orWhere('org_season.season_name','like',$search.'%')
              ->orWhere('merc_color_options.color_option','like',$search.'%');
          })

          /*->orWhere('style_creation.style_no'  , 'like', $search.'%' )
          ->orWhere('merc_bom_stage.bom_stage_description','like',$search.'%')
          ->orWhere('org_season.season_name','like',$search.'%')
          ->orWhere('merc_color_options.color_option','like',$search.'%')*/
          ->orderBy($order_column, $order_type)
          ->offset($start)->limit($length)->get();

          $costing_count = Costing::join('style_creation', 'style_creation.style_id', '=', 'costing.style_id')
          ->join('merc_bom_stage', 'merc_bom_stage.bom_stage_id', '=', 'costing.bom_stage_id')
          ->join('org_season', 'org_season.season_id', '=', 'costing.season_id')
          ->join('merc_color_options', 'merc_color_options.col_opt_id', '=', 'costing.color_type_id')
          ->where('costing.created_by', '=', $user_id)
          ->where(function ($query) use ($search) {
              $query->orWhere('costing.id', 'like', $search.'%' )
              ->orWhere('style_creation.style_no'  , 'like', $search.'%' )
              ->orWhere('merc_bom_stage.bom_stage_description','like',$search.'%')
              ->orWhere('org_season.season_name','like',$search.'%')
              ->orWhere('merc_color_options.color_option','like',$search.'%');
          })
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
      ->distinct()->select('article_no')->get()->pluck('article_no');
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


    private function get_color_id_from_name($color_name){
      if($color_name == null || $color_name == false || $color_name == ''){
        return null;
      }
      else{
         $color = Color::where('color_code', '=', $color_name)->first();
         return $color->color_id;
      }
    }


    private function autocomplete_search($search)	{
  		$ists = Costing::select('id','sc_no')
  		->where([['sc_no', 'like', '%' . $search . '%'], ['status', '=', 'APPROVED']]) ->get();
  		return $ists;
  	}


    private function get_item_from_article_no($article_no){
      $component_item = CostingFinishGoodComponentItem::where('article_no', '=', $article_no)->first();
      if($component_item == null || $component_item == ''){
        return null;
      }
      else{
        $item = Item::find($component_item->master_id);
        return $item;
      }
    }

    private function get_product_feature_components($style_id){
      $product_feature_components = DB::select("SELECT
        product_feature.product_feature_id,
        product_feature.product_feature_description,
        product_component.product_component_id,
        product_component.product_component_description,
        product_silhouette.product_silhouette_id,
        product_silhouette.product_silhouette_description,
        product_feature_component.feature_component_id
        FROM product_feature_component
				INNER JOIN product_feature ON product_feature.product_feature_id = product_feature_component.product_feature_id
        INNER JOIN product_silhouette ON product_silhouette.product_silhouette_id = product_feature_component.product_silhouette_id
        INNER JOIN product_component ON product_component.product_component_id = product_feature_component.product_component_id
        INNER JOIN style_creation ON style_creation.product_feature_id = product_feature.product_feature_id
        WHERE style_creation.style_id = ?", [$style_id]);

        return $product_feature_components;
    }

    /*private function generate_bom_for_costing($costing_id) {
      $deliveries = CustomerOrderDetails::where('costing_id', '=', $costing_id)->get();
      $costing = Costing::find($costing_id);
      for($y = 0; $y < sizeof($deliveries); $y++) {
        $bom = new BOMHeader();
        $bom->costing_id = $deliveries[$y]->costing_id;
        $bom->delivery_id = $deliveries[$y]->details_id;
        $bom->sc_no = $costing->sc_no;
        $bom->status = 1;
        $bom->save();

        $components = CostingFinishGoodComponent::where('fg_id', '=', $deliveries[$y]->fg_id)->get()->pluck('id');
        $items = CostingFinishGoodComponentItem::whereIn('fg_component_id', $components)->get();
        $items = json_decode(json_encode($items), true); //conver to array
        for($x = 0 ; $x < sizeof($items); $x++) {
          $items[$x]['bom_id'] = $bom->bom_id;
          $items[$x]['costing_item_id'] = $items[$x]['id'];
          $items[$x]['id'] = 0; //clear id of previous data, will be auto generated
          $items[$x]['bom_unit_price'] = $items[$x]['unit_price'];
          $items[$x]['order_qty'] = $deliveries[$y]->order_qty * $items[$x]['gross_consumption'];
          $items[$x]['required_qty'] = $deliveries[$y]->order_qty * $items[$x]['gross_consumption'];
          $items[$x]['total_cost'] = (($items[$x]['unit_price'] * $items[$x]['gross_consumption'] * $deliveries[$y]->order_qty) + $items[$x]['freight_charges'] + $items[$x]['surcharge']);
          $items[$x]['created_date'] = null;
          $items[$x]['created_by'] = null;
          $items[$x]['updated_date'] = null;
          $items[$x]['updated_by'] = null;
        }
        DB::table('bom_details')->insert($items);
      }
    }*/



}
