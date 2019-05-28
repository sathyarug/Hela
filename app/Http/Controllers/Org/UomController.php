<?php

namespace App\Http\Controllers\Org;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use App\Models\Org\UOM;
use Exception;
use App\Libraries\AppAuthorize;

class UomController extends Controller
{
    var $authorize = null;

    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
      $this->authorize = new AppAuthorize();
    }

    //get UOM list
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


    //create a UOM
    public function store(Request $request)
    {
      if($this->authorize->hasPermission('UOM_MANAGE'))//check permission
      {
        $uom = new UOM();
        if($uom->validate($request->all()))
        {
          $uom->fill($request->all());
          $uom->status = 1;
          $uom->save();

          return response([ 'data' => [
            'message' => 'UOM saved successfully',
            'uom' => $uom
            ]
          ], Response::HTTP_CREATED );
        }
        else
        {
            $errors = $uom->errors();// failure, get errors
            return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //get a UOM
    public function show($id)
    {
      if($this->authorize->hasPermission('UOM_MANAGE'))//check permission
      {
        $uom = UOM::find($id);
        if($uom == null)
          throw new ModelNotFoundException("Requested UOM not found", 1);
        else
          return response([ 'data' => $uom ]);
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //update a UOM
    public function update(Request $request, $id)
    {
      if($this->authorize->hasPermission('UOM_MANAGE'))//check permission
      {
        $uom = UOM::find($id);
        if($uom->validate($request->all()))
        {
          $uom->fill($request->except('uom_code'));
          $uom->save();

          return response([ 'data' => [
            'message' => 'UOM updated successfully',
            'uom' => $uom
          ]]);
        }
        else
        {
          $errors = $uom->errors();// failure, get errors
          return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //deactivate a UOM
    public function destroy($id)
    {
      if($this->authorize->hasPermission('UOM_DELETE'))//check permission
      {
        $uom = UOM::where('uom_id', $id)->update(['status' => 0]);
        return response([
          'data' => [
            'message' => 'UOM was deactivated successfully.',
            'uom' => $uom
          ]
        ] , Response::HTTP_NO_CONTENT);
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //validate anything based on requirements
    public function validate_data(Request $request){
      $for = $request->for;
      if($for == 'duplicate')
      {
        return response($this->validate_duplicate_code($request->uom_id , $request->uom_code));
      }
    }


    //check UOM code already exists
    private function validate_duplicate_code($id , $code)
    {
      $uom = UOM::where('uom_code','=',$code)->first();
      if($uom == null){
        return ['status' => 'success'];
      }
      else if($uom->uom_id == $id){
        return ['status' => 'success'];
      }
      else {
        return ['status' => 'error','message' => 'UOM code already exists'];
      }
    }


    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
      $query = null;
      if($fields == null || $fields == '') {
        $query = UOM::select('*');
      }
      else{
        $fields = explode(',', $fields);
        $query = UOM::select($fields);
        if($active != null && $active != ''){
          $query->where([['status', '=', $active]]);
        }
      }
      return $query->get();
    }

    //search UOM for autocomplete
    private function autocomplete_search($search)
  	{
  		$uom_lists = UOM::select('uom_id','uom_code')
  		->where([['uom_code', 'like', '%' . $search . '%'],]) ->get();
  		return $uom_lists;
  	}


    //get searched UOMs for datatable plugin format
    private function datatable_search($data)
    {
      if($this->authorize->hasPermission('UOM_MANAGE'))//check permission
      {
        $start = $data['start'];
        $length = $data['length'];
        $draw = $data['draw'];
        $search = $data['search']['value'];
        $order = $data['order'][0];
        $order_column = $data['columns'][$order['column']]['data'];
        $order_type = $order['dir'];

        $uom_list = UOM::select('*')
        ->where('uom_code'  , 'like', $search.'%' )
        ->orWhere('uom_description'  , 'like', $search.'%' )
        ->orderBy($order_column, $order_type)
        ->offset($start)->limit($length)->get();

        $uom_count = UOM::where('uom_code'  , 'like', $search.'%' )
        ->orWhere('uom_description'  , 'like', $search.'%' )
        ->count();

        return [
            "draw" => $draw,
            "recordsTotal" => $uom_count,
            "recordsFiltered" => $uom_count,
            "data" => $uom_list
        ];
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }

}
