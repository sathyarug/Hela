<?php

namespace App\Http\Controllers\Org;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use App\Models\Org\Store;
use Exception;

class StoreController extends Controller
{
    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
    }

    //get Store list
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


    //create a Store
    public function store(Request $request)
    {
      $store = new Store();
      if($store->validate($request->all()))
      {
        $store->fill($request->all());
        $store->status = 1;
        $store->save();

        return response([ 'data' => [
          'message' => 'Store was saved successfully',
          'store' => $store
          ]
        ], Response::HTTP_CREATED );
      }
      else
      {
          $errors = $store->errors();// failure, get errors
          return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }


    //get a Store
    public function show($id)
    {
      $store = Store::find($id);
      if($store == null)
        throw new ModelNotFoundException("Requested store not found", 1);
      else
        return response([ 'data' => $store ]);
    }


    //update a Store
    public function update(Request $request, $id)
    {
      $store = Store::find($id);
      if($store->validate($request->all()))
      {
        $store->fill($request->except('store_name'));
        $store->save();

        return response([ 'data' => [
          'message' => 'Store was updated successfully',
          'store' => $store
        ]]);
      }
      else
      {
        $errors = $store->errors();// failure, get errors
        return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }


    //deactivate a Store
    public function destroy($id)
    {
      $store = Store::where('store_id', $id)->update(['status' => 0]);
      return response([
        'data' => [
          'message' => 'Store was deactivated successfully.',
          'store' => $store
        ]
      ] , Response::HTTP_NO_CONTENT);
    }


    //validate anything based on requirements
    public function validate_data(Request $request){
      $for = $request->for;
      if($for == 'duplicate')
      {
        return response($this->validate_duplicate_code($request->store_id , $request->store_name));
      }
    }


    //check Store code already exists
    private function validate_duplicate_code($id , $code)
    {
      $store = Store::where('store_name','=',$code)->first();
      if($store == null){
        return ['status' => 'success'];
      }
      else if($store->store_id == $id){
        return ['status' => 'success'];
      }
      else {
        return ['status' => 'error','message' => 'Store code already exists'];
      }
    }


    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
      $query = null;
      if($fields == null || $fields == '') {
        $query = Store::select('*');
      }
      else{
        $fields = explode(',', $fields);
        $query = Store::select($fields);
        if($active != null && $active != ''){
          $query->where([['status', '=', $active]]);
        }
      }
      return $query->get();
    }

    //search Store for autocomplete
    private function autocomplete_search($search)
  	{
  		$store_lists = Store::select('store_id','store_name')
  		->where([['store_name', 'like', '%' . $search . '%'],]) ->get();
  		return $store_lists;
  	}


    //get searched Stores for datatable plugin format
    private function datatable_search($data)
    {
      $start = $data['start'];
      $length = $data['length'];
      $draw = $data['draw'];
      $search = $data['search']['value'];
      $order = $data['order'][0];
      $order_column = $data['columns'][$order['column']]['data'];
      $order_type = $order['dir'];

      $store_list = Store::join('org_location' , 'org_location.loc_id' , '=' , 'org_store.loc_id')
      ->select('org_store.*','org_location.loc_name')
      ->where('store_name'  , 'like', $search.'%' )
      ->orWhere('loc_name'  , 'like', $search.'%' )
      ->orderBy($order_column, $order_type)
      ->offset($start)->limit($length)->get();

      $store_count = Store::join('org_location' , 'org_location.loc_id' , '=' , 'org_store.loc_id')
      ->where('store_name'  , 'like', $search.'%' )
      ->orWhere('loc_name'  , 'like', $search.'%' )
      ->count();

      return [
          "draw" => $draw,
          "recordsTotal" => $store_count,
          "recordsFiltered" => $store_count,
          "data" => $store_list
      ];
    }

}
