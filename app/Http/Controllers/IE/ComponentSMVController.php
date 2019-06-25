<?php

namespace App\Http\Controllers\IE;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use App\Models\IE\ComponentSMVHeader;
use App\Models\IE\ComponentSMVDetails;
use App\Models\Merchandising\BulkCostingFeatureDetails;
use App\Models\IE\GarmentOperationMaster;
use App\Models\IE\SMVUpdateHistory;
use App\Models\Merchandising\StyleCreation;
use Exception;

class ComponentSMVController extends Controller
{
    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
    }

    //get Service Type list
    public function index(Request $request)
    {
      $type = $request->type;
      if($type == 'datatable')   {
        $data = $request->all();
        return response($this->datatable_search($data));
      }
      else if($type == 'searchDetails')    {
        $styleId = $request->styleId;
        $bomStageId=$request->bomStageId;
        return response(['data'=>$this->details_search($styleId,$bomStageId)]);
      }
      else if($type=='checkSMVRange'){
        $styleId=$request->styleId;
        $styleWiseTotalSMV=$request->styleWiseTotalSMV;
        return ($this->check_smv_range($styleId,$styleWiseTotalSMV));
      }
      else {
        $active = $request->active;
        $fields = $request->fields;
        return response([
          'data' => $this->list($active , $fields)
        ]);
      }
    }


    //create a Service Type
    public function storeDataset(Request $request)
    {
      $styleId=$request->styleId;
      $bomStageID=$request->bomStageId;
      $totalSMV=$request->totalSMV;
      $details=$request->data;
      $smvComponentHeader=new ComponentSMVHeader();
      $smvComponentHeader->style_id=$styleId;
      $smvComponentHeader->status=1;
      $smvComponentHeader->bom_stage_id=$bomStageID;
      $smvComponentHeader->total_smv=$totalSMV;
      $smvComponentHeader->save();
      $headerId=$smvComponentHeader->smv_component_header_id;

      //echo(sizeof($details));
      for($i=0;$i<sizeof($details);$i++){
        $smvComponentDetails=new ComponentSMVDetails();
        $smvComponentDetails->smv_component_header_id=$headerId;
        $garmentOperationName=$details[$i]["garment_operation_name"];
        //echo($garmentOperationName);
         $garmentOperation=GarmentOperationMaster::select('*')
         ->where('garment_operation_name','=',$garmentOperationName)
         ->first();
         //echo($garmentOperation->garment_operation_id);
         $smvComponentDetails->garment_operation_id=$garmentOperation->garment_operation_id;
         $smvComponentDetails->product_feature_id=$details[$i]['product_feature_id'];
         $smvComponentDetails->smv=$details[$i]['smv'];
         $smvComponentDetails->status=1;
         $smvComponentDetails->save();
        }


    return response(['data'=>[
      'message'=>"Component SMV Saved sucessfully",
      ]
    ]);
    }

    public function check_smv_range($styleId,$styleWiseTotalSMV){



      $smvUpdateHistory=SMVUpdateHistory::join('style_creation','ie_smv_his.product_silhouette_id','=','style_creation.product_silhouette_id')
      ->where('style_creation.style_id','=',$styleId)
      ->where('ie_smv_his.min_smv','<=',$styleWiseTotalSMV)
      ->where('ie_smv_his.max_smv','>=',$styleWiseTotalSMV)
      ->select('*')
      ->first();
      //->toSql();
      //print_r($smvUpdateHistory);

      if($smvUpdateHistory==null){
        return response([
           'data' => [
             'message' => 'SMV is Not in the Range',
             'status' => 0,
           ]
         ]);

      }
      else if($smvUpdateHistory!=null){
         return response([
           'data' => [
             'message' => 'SMV is in the Range',
             'status' => 1,
           ]
         ]);
        //echo("pass");
      }


    }

    //get a Service Type
    public function show($id)
    {

      $garmentOperation = GarmentOperationMaster::find($id);
      if($garmentOperation == null)
        throw new ModelNotFoundException("Requested Garment Operation not found", 1);
      else
        return response([ 'data' => $garmentOperation ]);
    }






    //deactivate a Service Type
    public function destroy($id)
    {
      $garmentOperation = GarmentOperationMaster::where('garment_operation_id', $id)->update(['status' => 0]);
      return response([
        'data' => [
          'message' => 'Garment Operation was deactivated successfully.',
          'garmentOperation' => $garmentOperation
        ]
      ] , Response::HTTP_NO_CONTENT);
    }


    //validate anything based on requirements
    public function validate_data(Request $request){
      $for = $request->for;
      if($for == 'duplicate')
      {
        return response($this->validate_duplicate_code($request->garment_operation_id , $request->garment_operation_name));
      }
    }


    //check Service Type code already exists
    private function validate_duplicate_code($id , $code)
    {
      $garmentOperation = GarmentOperationMaster::where('garment_operation_name','=',$code)->first();
      if($garmentOperation == null){
        return ['status' => 'success'];
      }
      else if($garmentOperation->garment_operation_id == $id){
        return ['status' => 'success'];
      }
      else {
        return ['status' => 'error','message' => 'Garment Operation already exists'];
      }
    }


    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
      $query = null;
      if($fields == null || $fields == '') {
        $query = GarmentOperationMaster::select('*');
      }
      else{
        $fields = explode(',', $fields);
        $query = GarmentOperationMaster::select($fields);
        if($active != null && $active != ''){
          $query->where([['status', '=', $active]]);
        }
      }
      return $query->get();
    }

    //search Service Type for autocomplete
    private function autocomplete_search($search)
  	{
  		$garment_operation_lists = GarmentOperationMaster::select('garment_operation_id','garment_operation_name')
  		->where([['garment_operation_name', 'like', '%' . $search . '%'],]) ->get();
  		return $garment_operation_lists;
  	}
    private function details_search($styleId,$bomStageID){
      //echo($styleId);
      //echo($bomStageID);
      $costingDetails= BulkCostingFeatureDetails::join('merc_bom_stage','costing_bulk_feature_details.bom_stage','=','merc_bom_stage.bom_stage_id')
      ->join('costing_bulk','costing_bulk_feature_details.bulkheader_id','=','costing_bulk.bulk_costing_id')
      ->join('style_creation','costing_bulk.style_id','=','style_creation.style_id')
      ->join('product_feature','costing_bulk_feature_details.feature_id','=','product_feature.product_feature_id')
      ->select('product_feature.product_feature_description','product_feature.product_feature_id')
      ->where('style_creation.style_id','=',$styleId)
      ->where('merc_bom_stage.bom_stage_id','=',$bomStageID)
      ->get();
      return $costingDetails;

    }

    //get searched Service Types for datatable plugin format
    private function datatable_search($data)
    {
      $start = $data['start'];
      $length = $data['length'];
      $draw = $data['draw'];
      $search = $data['search']['value'];
      $order = $data['order'][0];
      $order_column = $data['columns'][$order['column']]['data'];
      $order_type = $order['dir'];

      $garment_operation_list = GarmentOperationMaster::select('*')
      ->where('garment_operation_name'  , 'like', $search.'%' )
      ->orderBy($order_column, $order_type)
      ->offset($start)->limit($length)->get();

      $garment_operation_count = GarmentOperationMaster::where('garment_operation_name'  , 'like', $search.'%' )
      ->count();

      return [
          "draw" => $draw,
          "recordsTotal" => $garment_operation_count,
          "recordsFiltered" => $garment_operation_count,
          "data" => $garment_operation_list
      ];
    }

}
