<?php

namespace App\Http\Controllers\Merchandising\BulkCosting;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Merchandising\BulkCosting;
use App\Models\Merchandising\BulkCostingApproval;
use App\Models\Merchandising\BulkCostingDetails;
use App\Models\Merchandising\BulkCostingFeatureDetails;
use App\Models\Merchandising\CostingBulkRevision;
use App\Models\Merchandising\HisBulkCosting;
use App\Models\Merchandising\HisBulkCostingDetails;
use App\Models\Merchandising\HisBulkCostingFeatureDetails;
use App\Models\Merchandising\StyleProductFeature;


class BulkCostingController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $type = $request->type;

        if ($type == 'getSeasonList') {
            return response($this->getSeasonList());
        } elseif ($type == 'getColorType') {
            return response($this->getColorType());
        }elseif ($type == 'getBomStage') {
            return response($this->getBomStage());
        } elseif ($type == 'auto') {
            $search = $request->search;
            return response($this->getStyleList($search));
        } elseif ($type == 'getStyleData') {
            return response($this->getStyleData($request->style_id));
        }elseif($type == 'getCostListing'){
            return response($this->getCostSheetListing($request->style_id));
        }elseif($type == 'getCostingHeader'){
            return response($this->getCostingHeaderDetails($request->costing_id));
        } elseif ($type == 'getFinishGood') {
            $data=array('blkNo'=>$request->blk,'bom'=>$request->bom,'season'=>$request->sea,'colType'=>$request->col);
            return response($this->getFinishGood($request->style_id,$data));
        }elseif ($type == 'SentToApproval') {
            $data=array('blkNo'=>$request->blk,'bom'=>$request->bom,'season'=>$request->sea,'colType'=>$request->col);
            return response($this->SentToApproval($request->style_id,$data));
        }elseif ($type == 'revision') {
            $data=array('blkNo'=>$request->blk,'bom'=>$request->bom,'season'=>$request->sea,'colType'=>$request->col);
            return response($this->revision($request->style_id,$data));
        }elseif ($type == 'item'){
            $search = $request->search;
            return response($this->getItemList($search));
        }elseif ($type == 'getItemData'){
            $item = $request->item;
            return response($this->getItemDetails($item));
        }elseif ($type == 'getColorForDivision'){
            $division_id = $request->division_id;
            $query = $request->query;
            return response($this->getColorForDivision($division_id,$query));
        }elseif ($type == 'getColorForDivisionCode'){
            $division_id = $request->division_id;
            $query = $request->query;
            return response($this->getColorForDivisionCode($division_id,$query));
        }elseif ($type == 'apv'){
            $this->Approval($request);
        }elseif ($type == 'report-balk'){
            $this->reportBalk($request);
        }elseif ($type == 'report-flash'){
            $this->reportFlash($request);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        echo 'Create';
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        if($request->bulk_header !=0){
            $model = BulkCosting::find($request->bulk_header);
        }else{
             $model = new BulkCosting();
        }

        $type = $request->type;

        if($type =='lineHeader'){
            $data=array('blkNo'=>$request->hader,'bom'=>$request->bom,'season'=>$request->sea,'colType'=>$request->col);
            return response($this->saveLineHeader($request,$data));
        }

        if ($model->validate($request->all())) {
            $model->style_id=$request->Style['style_id'];

            $date=date_create($request->pcd);

            $model->pcd=date_format($date,"Y-m-d");
            $model->fob=$request->fob;
            $model->plan_efficiency=$request->plan_efficiency;
            $model->finance_charges=$request->finance_charges;
            $model->cost_per_std_min=$request->cost_per_std_min;
            $model->epm=$request->epm;
            $model->np_margin=$request->np_margin;
            $model->cost_per_min=$request->cost_per_min;
            $model->finance_charges=$request->finance_charges;

            $model->status = 1;

//            print_r($request->all());exit;
            $model->save();
            return response(['data' => [
                    'message' => 'Costing is saved successfully',
                    'bulkCostin' => $model
                ]
                    ], Response::HTTP_CREATED);
        } else {
            $errors = $model->errors(); // failure, get errors
            return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        echo 'Show';
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        echo 'Edit';
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        $modelOld = BulkCostingFeatureDetails::find($id);


        $styleFeatures=BulkCostingFeatureDetails::where('style_feature_id',$modelOld->style_feature_id)->where('feature_id',$modelOld->feature_id)->where('status',1)->get()->toArray();

        $featureList=\App\Models\Org\FeatureComponent::where('product_feature_id',$modelOld->feature_id)->where('status',1)->get()->toArray();

        foreach ($featureList AS $feature ){
        $chrck=BulkCostingFeatureDetails::where('style_feature_id',$modelOld->style_feature_id)->where('feature_id',$feature['product_feature_id'])->where('component_id',$feature['product_component_id'])->get()->toArray();

            if( count($chrck)== 0){
                return 0;
            }

       }

        $productFeatureList = new StyleProductFeature();
        $productFeatureList->style_id = $request->style_id;
        $productFeatureList->product_feature_id = $modelOld->feature_id;
        $productFeatureList->save();

        foreach ($styleFeatures AS $styleFeature) {

           $model = new BulkCostingFeatureDetails();

           $model->style_feature_id = $productFeatureList->id;
           $model->feature_id = $styleFeature['feature_id'];
           $model->component_id = $styleFeature['component_id'];
           $model->bulkheader_id = $styleFeature['bulkheader_id'];
           $model->surcharge = $styleFeature['surcharge'];
           $model->color_ID = $styleFeature['color_ID'];
           $model->season_id = $styleFeature['season_id'];
           $model->col_opt_id = $styleFeature['col_opt_id'];
           $model->bom_stage = $styleFeature['bom_stage'];
           $model->mcq = $styleFeature['mcq'];
           $model->combo_code = $styleFeature['combo_code'];
           $model->save();

           $itemList = BulkCostingDetails::select('*')
               ->where([['bulkheader_id', '=', $styleFeature['blk_feature_id']], ['status', '=', 1]])->get();

           foreach ($itemList AS $item) {
               $BulkCostingDetails = new BulkCostingDetails();

               $BulkCostingDetails->bulkheader_id = $model->blk_feature_id;
               $BulkCostingDetails->article_no = $item->article_no;
               $BulkCostingDetails->color_id = $item->color_id;
               $BulkCostingDetails->color_type_id = $item->color_type_id;
               $BulkCostingDetails->code = $item->code;
               $BulkCostingDetails->main_item = $item->main_item;
               $BulkCostingDetails->supplier_id = $item->supplier_id;
               $BulkCostingDetails->position = $item->position;
               $BulkCostingDetails->measurement = $item->measurement;
               $BulkCostingDetails->process_option = $item->process_option;
               $BulkCostingDetails->uom_id = $item->uom_id;
               $BulkCostingDetails->net_consumption = $item->net_consumption;
               $BulkCostingDetails->unit_price = $item->unit_price;
               $BulkCostingDetails->wastage = $item->wastage;
               $BulkCostingDetails->gross_consumption = $item->gross_consumption;
               $BulkCostingDetails->freight_charges = $item->freight_charges;
               $BulkCostingDetails->finance_charges = $item->finance_charges;
               $BulkCostingDetails->mcq = $item->mcq;
               $BulkCostingDetails->moq = $item->moq;
               $BulkCostingDetails->calculate_by_deliverywise = $item->calculate_by_deliverywise;
               $BulkCostingDetails->order_type = $item->order_type;
               $BulkCostingDetails->surcharge = $item->surcharge;
               $BulkCostingDetails->total_cost = $item->total_cost;
               $BulkCostingDetails->shipping_terms = $item->shipping_terms;
               $BulkCostingDetails->lead_time = $item->lead_time;
               $BulkCostingDetails->country_of_origin = $item->country_of_origin;
               $BulkCostingDetails->comments = $item->comments;

               $BulkCostingDetails->save();

           }
       }
        $data=array('blkNo'=>$model->bulkheader_id,'bom'=>$model->bom_stage,'season'=>$model->season_id,'colType'=>$model->col_opt_id);
         return $this->getFinishGood($request->style_id,$data);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        echo 'Destroy';
    }

    private function getSeasonList() {
        //return \App\Models\Org\Customer::getActiveCustomerList();
        return \App\Models\Org\Season::select('season_id', 'season_name')
                        ->where([['status', '=', 1]])->get();
    }

    private function getColorType() {
        return \App\Models\Merchandising\ColorOption::select('col_opt_id', 'color_option')
                        ->where([['status', '=', 1]])->get();
    }

    private function getBomStage() {
        return \App\Models\Merchandising\BOMStage::select('bom_stage_id', 'bom_stage_description')
            ->where([['status', '=', 1]])->get();
    }

    private function getStyleList($search) {
        return \App\Models\Merchandising\StyleCreation::select('style_id', 'style_no')
                        ->where([['style_no', 'like', '%' . $search . '%'],])->get();
    }

    private function getStyleData($style_id) {
       $dataArr = array();
        $styleData = \App\Models\Merchandising\StyleCreation::find($style_id);
        $hader = \App\Models\Merchandising\BulkCosting::where('style_id', $style_id)->get()->toArray();
        $country = \App\Models\Org\Country::find($styleData->customer->customer_country);


        $dataArr['style_remark'] = $styleData->remark;
        $dataArr['division_name'] = $styleData->division->division_description;
        $dataArr['division_id'] = $styleData->division->division_id;
        $dataArr['style_desc'] = $styleData->style_description;
        $dataArr['cust_name'] = $styleData->customer->customer_name;


        $dataArr['style_desc'] = $styleData->style_description;
        $dataArr['style_id'] = $styleData->style_id;
        $dataArr['style_no'] = $styleData->style_no;
        $dataArr['image'] = $styleData->image;

        $dataArr['cust_id'] = $styleData->customer->customer_id;
        $dataArr['division_name'] = $styleData->division->division_description;
        $dataArr['division_id'] = $styleData->division->division_id;

        //echo json_encode($styleData->customer);
        $dataArr['country'] = $country->country_description;



       // $sumStyleSmvComp=\App\Models\ie\StyleSMV::where('style_id', $styleData->style_id)->orderBy('smv_comp_id', 'desc')->first();

        //$sumStyleSmvComp=\App\Models\ie\StyleSMV::where('style_id', $styleData->style_id)->orderBy('smv_comp_id', 'desc')->first();
        //  echo json_encode($styleData->style_id);

//        dd($sumStyleSmvComp->created_date);exit;

        if(count($hader)>0){
            $costed_smv=0;
            $blkCostFea = \App\Models\Merchandising\BulkCostingFeatureDetails::where('bulkheader_id',$hader[0]['bulk_costing_id'])->where('status',1)->get();

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

        }



        return $dataArr;
    }


    private function getFinishGood($style_id,$data) {


        $productFeatureList = \App\Models\Merchandising\StyleProductFeature::where('style_id', $style_id)->get()->toArray();

        $count=1;
        $lineNo=0;
        foreach ($productFeatureList AS $productFeature){

            $featureList=\App\Models\Org\FeatureComponent::where('product_feature_id', $productFeature['product_feature_id'])->where('status',1)->get()->toArray();

            //$cal=$this->getEmpNp($productFeature['product_feature_id'],$data);



            foreach ($featureList As $feature){
                $surcharge=false;
                $mcq=false;
                $colordata='';$blkHeadId=0;$colorComboData='';$smv=0;
                $featureData=\App\Models\Org\Feature::find($feature['product_feature_id']);
                $component=\App\Models\Org\Component::find($feature['product_component_id']);

                $blk=$data['blkNo'];
                $bom=$data['bom'];
                $season=$data['season'];
                $colType=$data['colType'];

                $blkCostFea = \App\Models\Merchandising\BulkCostingFeatureDetails::where('style_feature_id', $productFeature['id'])->where('feature_id', $featureData->product_feature_id)->where('component_id',$component->product_component_id)->where('bulkheader_id',$blk)->where('bom_stage',$bom)->where('season_id',$season)->where('col_opt_id',$colType)->where('status',1)->first();
                if(isset($blkCostFea->mcq) && $blkCostFea->mcq==1){
                        $mcq=true;
                    }else{
                        $mcq=false;
                    }

                if(isset($blkCostFea->surcharge) && $blkCostFea->surcharge==1){
                    $surcharge=true;
                }else{
                    $surcharge=false;
                }


                if(isset($blkCostFea->color_ID)){

                    $color=\App\Models\Org\Color::find($blkCostFea->color_ID)->first();
                    $colordata=$color->color_name;

                }
                if(isset($blkCostFea->combo_color)){

                   $color=\App\Models\Org\Color::find($blkCostFea->combo_color);
                    $colorComboData=$color->color_code;
                }
                if(isset($blkCostFea->blk_feature_id)){
                    $blkHeadId=$blkCostFea->blk_feature_id;
                }

                if(isset($blkCostFea->smv) && (!is_null($blkCostFea->smv))){
                	$smv=$blkCostFea->smv;
                }


                $productFeatureArray[]=array(
                    'pack_name'=>'PACK-'.$count,
                    'id'=>$component->product_component_id,
                    'style_feature_id'=>$productFeature['id'],
                    'description'=>$component->product_component_description,
                    'main_featureName'=>$featureData->product_feature_description,
                    'mcq'=>$mcq,
                    'surcharge'=>$surcharge,
                    'color'=>$colordata,
                    'color_combo'=>$colorComboData,
                    'blkHead'=>$blkHeadId,
                    'smv'=>$smv,
                    'main_featureName_id'=>$featureData->product_feature_id,
                    'success'=>'<a  style="min-height: 12px !important;padding: 1px 10px;font-size: 6px; line-height: 1; border-radius: 2px;margin: 1px;"  class="btn bg-success-400 btn-rounded  btn-icon btn-xs-new"><i class="letter-icon">save</i> </a>',
                    'primary'=>'<a  style="min-height: 12px !important;padding: 1px 10px;font-size: 6px; line-height: 1; border-radius: 2px;margin: 1px;"  class="btn bg-primary-400 btn-rounded  btn-icon btn-xs-new"><i class="letter-icon">Copy</i> </a>',
                    'danger'=>'<a  style="min-height: 12px !important;padding: 1px 10px;font-size: 6px; line-height: 1; border-radius: 2px;margin: 1px;"  class="btn bg-warning-400 btn-rounded  btn-icon btn-xs-new"><i class="letter-icon">Open</i> </a>'

                );$lineNo++;
            }


$count++;
        }

        return json_encode($productFeatureArray);
    }

    private function getEmpNp($product_feature_id,$data) {

        $blk=$data['blkNo'];
        $bom=$data['bom'];
        $season=$data['season'];
        $colType=$data['colType'];


        $getTotel=DB::select('SELECT
Sum((costing_bulk_details.unit_price*costing_bulk_details.gross_consumption)) AS total
FROM
costing_bulk_feature_details
INNER JOIN costing_bulk_details ON costing_bulk_details.bulkheader_id = costing_bulk_feature_details.blk_feature_id
INNER JOIN costing_bulk ON costing_bulk.bulk_costing_id = costing_bulk_feature_details.bulkheader_id
WHERE costing_bulk.bulk_costing_id='.$blk.' AND costing_bulk_feature_details.season_id='.$season.' AND costing_bulk_feature_details.col_opt_id='.$colType.' AND costing_bulk_feature_details.bom_stage='.$bom);
        print_r('SELECT
Sum((costing_bulk_details.unit_price*costing_bulk_details.gross_consumption)) AS total
FROM
costing_bulk_feature_details
INNER JOIN costing_bulk_details ON costing_bulk_details.bulkheader_id = costing_bulk_feature_details.blk_feature_id
INNER JOIN costing_bulk ON costing_bulk.bulk_costing_id = costing_bulk_feature_details.bulkheader_id
WHERE costing_bulk.bulk_costing_id='.$blk.' AND costing_bulk_feature_details.season_id='.$season.' AND costing_bulk_feature_details.col_opt_id='.$colType.' AND costing_bulk_feature_details.bom_stage='.$bom);exit;

    }


    private function SentToApproval($style_id,$data) {
        $blk = \App\Models\Merchandising\BulkCosting::find($data['blkNo']);
        $blk->costing_status='SentToApproval';
        $blk->save();
        $bulk_costing_id =$blk->bulk_costing_id;

        $keyVal = $bulk_costing_id;
        $keyCode=md5($keyVal);
        $BulkCostingApproval = new BulkCostingApproval;
        $BulkCostingApproval->costing_id = $blk->bulk_costing_id;
        $BulkCostingApproval->approval_key = $keyCode;
        $BulkCostingApproval->save();

        return($this->getFinishGood($style_id,$data));
    }

    private function revision($style_id,$data) {
        $max=CostingBulkRevision::where('costing_id',$data['blkNo'])->max('revision');
        $newMax=$max+1;

        $CostingBulkRevision = new CostingBulkRevision();
        $CostingBulkRevision->costing_id=$data['blkNo'];
        $CostingBulkRevision->revision=$newMax;
        $CostingBulkRevision->save();
        $blk = \App\Models\Merchandising\BulkCosting::find($data['blkNo']);

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

        $BulkCostingFeatureDetails= \App\Models\Merchandising\BulkCostingFeatureDetails::where('bulkheader_id', $data['blkNo'])->get();
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
        $blk = \App\Models\Merchandising\BulkCosting::find($data['blkNo']);
        $blk->costing_status='Edit';
        $blk->save();
        return($this->getFinishGood($style_id,$data));
    }

    private function saveLineHeader($request,$data){
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

        $model = BulkCosting::find($bulkCostingDetails->bulkheader_id);

        $data=array('blkNo'=>$data['blkNo'],'bom'=>$data['bom'],'season'=>$data['season'],'colType'=>$data['colType']);

        return $this->getFinishGood($model->style_id,$data);

//        return $bulkCostingDetails->blk_feature_id;

    }

    public function getItemList($search){
        return \App\itemCreation::select('master_id', 'master_description')
            ->where([['master_description', 'like', '%' . $search . '%'],])->get();
    }

    public function getItemDetails($id){
        $master= \App\itemCreation::find($id)->toArray();
        $SubCategory= \App\Models\Finance\Item\SubCategory::find($master['subcategory_id'])->toArray();
        $category= \App\Models\Finance\Item\Category::find($SubCategory['category_id'])->toArray();
        $supplier= \App\Models\Org\Supplier::where('status', 1)->get()->toArray();
        $serviceType= \App\Models\IE\ServiceType::where('status', 1)->get()->toArray();

        return array('category'=>$category,'supplier'=>$supplier,'pOptions'=>$serviceType);
    }

    public  function getColorForDivision($division_id,$query){
        $color=\App\Models\Org\Color::where([['division_id','=',$division_id]])->pluck('color_name')->toArray();
        return json_encode($color);
    }

    public  function getColorForDivisionCode($division_id,$query){
        $color=\App\Models\Org\Color::where([['division_id','=',$division_id]])->pluck('color_code')->toArray();
        return json_encode($color);
    }

    public  function Approval($request){
        $BulkCosting = BulkCosting::find($request->blk);
        if(isset($request->ur)){
            if($BulkCosting->costing_status =='SentToApproval'){
//            $CostingBulkRevision=BulkCostingApproval::where('costing_id',$request->blk)->where('approval_key',$request->approval_key)->where('id',$request->aid)->get();
                $blkApp = \App\Models\Merchandising\BulkCostingApproval::find($request->aid);
                $blkApp->lock_status=1;
                $blkApp->save();


                $upate =DB::table('costing_bulk')
                    ->where('bulk_costing_id', $blkApp->costing_id)
                    ->update(['costing_approval_user' => $request->ur,'costing_approval_time'=>now(),'costing_status'=>'approved']);

//            $user = auth()->user();
            }else{

            }
        }

    }
    public  function reportBalk($request){

        $getAllData=DB::select('SELECT
item_master.master_description,
item_master.master_code,
item_subcategory.subcategory_name,
item_category.category_name,
costing_bulk.style_id,
costing_bulk.bulk_costing_id,
costing_bulk_feature_details.blk_feature_id,
costing_bulk_details.item_id,
costing_bulk.pcd,
costing_bulk.plan_efficiency,
costing_bulk.fob,
costing_bulk.finance_charges,
costing_bulk.cost_per_min,
costing_bulk.cost_per_std_min,
costing_bulk.epm,
costing_bulk.np_margin,
sum((costing_bulk_details.unit_price*costing_bulk_details.gross_consumption)) AS total,
\'\' AS updated_date,
\'\' AS User
FROM
costing_bulk_details
INNER JOIN item_master ON item_master.master_id = costing_bulk_details.main_item
INNER JOIN item_subcategory ON item_master.subcategory_id = item_subcategory.subcategory_id
INNER JOIN item_category ON item_category.category_id = item_subcategory.category_id
INNER JOIN costing_bulk_feature_details ON costing_bulk_feature_details.blk_feature_id = costing_bulk_details.bulkheader_id
INNER JOIN costing_bulk ON costing_bulk_feature_details.bulkheader_id = costing_bulk.bulk_costing_id
WHERE
costing_bulk.style_id ='.$request->style_id.'
GROUP BY
item_category.category_name');

        $getAllDataHis=DB::select('SELECT
costing_bulk.style_id,
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
costing_bulk
INNER JOIN costing_bulk_revision ON costing_bulk.bulk_costing_id = costing_bulk_revision.costing_id
INNER JOIN his_costing_bulk ON costing_bulk_revision.revision = his_costing_bulk.revistion_id AND costing_bulk_revision.costing_id = his_costing_bulk.bulk_costing_id
INNER JOIN his_costing_bulk_feature_details ON his_costing_bulk.revistion_id = his_costing_bulk_feature_details.revistion_id AND his_costing_bulk.bulk_costing_id = his_costing_bulk_feature_details.bulkheader_id
INNER JOIN his_costing_bulk_details ON his_costing_bulk_feature_details.revistion_id = his_costing_bulk_details.revistion_id AND his_costing_bulk_feature_details.blk_feature_id = his_costing_bulk_details.bulkheader_id
INNER JOIN item_master ON item_master.master_id = his_costing_bulk_details.item_id
INNER JOIN item_subcategory ON item_master.subcategory_id = item_subcategory.subcategory_id
INNER JOIN item_category ON item_subcategory.category_id = item_category.category_id
INNER JOIN usr_profile ON usr_profile.user_id = costing_bulk_revision.created_by
WHERE
costing_bulk.style_id='.$request->style_id.'
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
//        dd($styleData->image);
print_r(json_encode(array('image'=>$styleData->image,'data'=>$fullData)));exit;

    }

    public  function reportFlash($request){
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
    }

    private function getCostSheetListing($styleId){

        $bulkHeaderData = BulkCosting::select(DB::raw("*, LPAD(bulk_costing_id,6,'0') AS CostingNo"))
                            ->where('style_id','=',$styleId)->get();
                          //  dd($bulkHeaderData);
        return $bulkHeaderData;
    }

    private function getCostingHeaderDetails($costingId){

       /* $costingHeader = BulkCosting::select("*")
                            ->where('bulk_costing_id','=',$costingId)->get();*/

        /*$costingHeader = \DB::table('costing_bulk')
                            ->join('org_season','org_season.season_id','=','costing_bulk.season_id')
                            ->select('costing_bulk.*','org_season.season_name')
                            ->where('costing_bulk.bulk_costing_id',$costingId)
                            ->get();*/
        
        $costingHeader = \DB::table('costing_bulk')
                            ->join('costing_bulk_feature_details','costing_bulk.bulk_costing_id','=','costing_bulk_feature_details.bulkheader_id')
                            ->join('org_season','org_season.season_id','=','costing_bulk_feature_details.season_id')
                            ->select('costing_bulk.*','org_season.season_name')
                            ->distinct()
                            ->where('costing_bulk.bulk_costing_id',$costingId)
                            ->get();

        return $costingHeader;

    }
}
