<?php

namespace App\Http\Controllers\IE;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use App\Models\IE\ServiceType;
use Exception;

class ServiceTypeController extends Controller
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
      else if($type == 'auto')    {
        $search = $request->search;
        return response($this->autocomplete_search($search));
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
      $servicetype = new ServiceType();
      if($servicetype->validate($request->all()))
      {
        $servicetype->fill($request->all());
        $servicetype->status = 1;
        $servicetype->save();

        return response([ 'data' => [
          'message' => 'Service Type was saved successfully',
          'servicetype' => $servicetype
          ]
        ], Response::HTTP_CREATED );
      }
      else
      {
          $errors = $servicetype->errors();// failure, get errors
          return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }


    //get a Service Type
    public function show($id)
    {

      $servicetype = ServiceType::find($id);
      if($servicetype == null)
        throw new ModelNotFoundException("Requested service type not found", 1);
      else
        return response([ 'data' => $servicetype ]);
    }


    //update a Service Type
    public function update(Request $request, $id)
    {
      $servicetype = ServiceType::find($id);
      if($servicetype->validate($request->all()))
      {
        $servicetype->fill($request->except('service_type_code'));
        $servicetype->save();

        return response([ 'data' => [
          'message' => 'Service Type was updated successfully',
          'servicetype' => $servicetype
        ]]);
      }
      else
      {
        $errors = $servicetype->errors();// failure, get errors
        return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }


    //deactivate a Service Type
    public function destroy($id)
    {
      $servicetype = ServiceType::where('service_type_id', $id)->update(['status' => 0]);
      return response([
        'data' => [
          'message' => 'Service Type was deactivated successfully.',
          'servicetype' => $servicetype
        ]
      ] , Response::HTTP_NO_CONTENT);
    }


    //validate anything based on requirements
    public function validate_data(Request $request){
      $for = $request->for;
      if($for == 'duplicate')
      {
        return response($this->validate_duplicate_code($request->service_type_id , $request->service_type_code));
      }
    }


    //check Service Type code already exists
    private function validate_duplicate_code($id , $code)
    {
      $servicetype = ServiceType::where('service_type_code','=',$code)->first();
      if($servicetype == null){
        return ['status' => 'success'];
      }
      else if($servicetype->service_type_id == $id){
        return ['status' => 'success'];
      }
      else {
        return ['status' => 'error','message' => 'Service Type code already exists'];
      }
    }


    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
      $query = null;
      if($fields == null || $fields == '') {
        $query = ServiceType::select('*');
      }
      else{
        $fields = explode(',', $fields);
        $query = ServiceType::select($fields);
        if($active != null && $active != ''){
          $query->where([['status', '=', $active]]);
        }
      }
      return $query->get();
    }

    //search Service Type for autocomplete
    private function autocomplete_search($search)
  	{
  		$service_type_lists = ServiceType::select('service_type_id','service_type_code')
  		->where([['service_type_code', 'like', '%' . $search . '%'],]) ->get();
  		return $service_type_lists;
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

      $service_type_list = ServiceType::select('*')
      ->where('service_type_code'  , 'like', $search.'%' )
      ->orWhere('service_type_description'  , 'like', $search.'%' )
      ->orderBy($order_column, $order_type)
      ->offset($start)->limit($length)->get();

      $service_type_count = ServiceType::where('service_type_code'  , 'like', $search.'%' )
      ->orWhere('service_type_description'  , 'like', $search.'%' )
      ->count();

      return [
          "draw" => $draw,
          "recordsTotal" => $service_type_count,
          "recordsFiltered" => $service_type_count,
          "data" => $service_type_list
      ];
    }

}
