<?php

namespace App\Http\Controllers\IE;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use App\Models\IE\CompoenentSMV;
use App\Models\Merchandising\BulkCostingFeatureDetails;
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
        return response($this->details_search($styleId,$bomStageId));
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
    public function store(Request $request)
    {
      $garmentOperation = new GarmentOperationMaster();
      if($garmentOperation->validate($request->all()))
      {
        $garmentOperation->fill($request->all());
        $garmentOperation->status = 1;
        $garmentOperation->save();

        return response([ 'data' => [
          'message' => 'Garment Operation successfully',
          'garmentOperation' => $garmentOperation
          ]
        ], Response::HTTP_CREATED );
      }
      else
      {
          $errors = $garmentOperation->errors();// failure, get errors
          return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
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
      $costingDetails= BulkCostingFeatureDetails::join('merc_bom_stage','costing_bulk_feature_details.bom_stage','=','merc_bom_stage.bom_stage_id')
      ->join('style_product_feature','costing_bulk_feature_details.style_feature_id','=','style_product_feature.id')
      //need to ad product feature
      //and join garmentOperation
      ->join('style_creation','costing_bulk_feature_details.style_id','=','style_creation.style_id')
      ->select('*')
      ->where('style_feature_id','=',$styleId)
      ->where('bom_stage','=',$bomStageID)


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
