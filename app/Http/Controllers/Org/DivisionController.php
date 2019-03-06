<?php

namespace App\Http\Controllers\Org;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use App\Models\Org\Division;
use Exception;

class DivisionController extends Controller
{
    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
    }

    //get Division list
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


    //create a Division
    public function store(Request $request)
    {
      $division = new Division();
      if($division->validate($request->all()))
      {
        $division->fill($request->all());
        $division->status = 1;
        $division->save();

        return response([ 'data' => [
          'message' => 'Division was saved successfully',
          'Division' => $division
          ]
        ], Response::HTTP_CREATED );
      }
      else
      {
          $errors = $division->errors();// failure, get errors
          return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }


    //get a Division
    public function show($id)
    {
      $division = Division::find($id);
      if($division == null)
        throw new ModelNotFoundException("Requested division not found", 1);
      else
        return response([ 'data' => $division ]);
    }


    //update a Division
    public function update(Request $request, $id)
    {
      $division = Division::find($id);
      if($division->validate($request->all()))
      {
        $division->fill($request->except('division_code'));
        $division->save();

        return response([ 'data' => [
          'message' => 'Division was updated successfully',
          'division' => $division
        ]]);
      }
      else
      {
        $errors = $division->errors();// failure, get errors
        return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }


    //deactivate a Division
    public function destroy($id)
    {
      $division = Division::where('division_id', $id)->update(['status' => 0]);
      return response([
        'data' => [
          'message' => 'Division was deactivated successfully.',
          'division' => $division
        ]
      ] , Response::HTTP_NO_CONTENT);
    }


    //validate anything based on requirements
    public function validate_data(Request $request){
      $for = $request->for;
      if($for == 'duplicate')
      {
        return response($this->validate_duplicate_code($request->division_id , $request->division_code));
      }
    }


    //check Division code already exists
    private function validate_duplicate_code($id , $code)
    {
      $division = Division::where('division_code','=',$code)->first();
      if($division == null){
        return ['status' => 'success'];
      }
      else if($division->division_id == $id){
        return ['status' => 'success'];
      }
      else {
        return ['status' => 'error','message' => 'Division code already exists'];
      }
    }


    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
      $query = null;
      if($fields == null || $fields == '') {
        $query = Division::select('*');
      }
      else{
        $fields = explode(',', $fields);
        $query = Division::select($fields);
        if($active != null && $active != ''){
          $query->where([['status', '=', $active]]);
        }
      }
      return $query->get();
    }

    //search Division for autocomplete
    private function autocomplete_search($search)
  	{
  		$division_lists = Division::select('division_id','division_description')
  		->where([['division_description', 'like', '%' . $search . '%'],]) ->get();
  		return $division_lists;
  	}


    //get searched Divisions for datatable plugin format
    private function datatable_search($data)
    {
      $start = $data['start'];
      $length = $data['length'];
      $draw = $data['draw'];
      $search = $data['search']['value'];
      $order = $data['order'][0];
      $order_column = $data['columns'][$order['column']]['data'];
      $order_type = $order['dir'];

      $division_list = Division::select('*')
      ->where('division_code'  , 'like', $search.'%' )
      ->orWhere('division_description'  , 'like', $search.'%' )
      ->orderBy($order_column, $order_type)
      ->offset($start)->limit($length)->get();

      $division_count = Division::where('division_code'  , 'like', $search.'%' )
      ->orWhere('division_description'  , 'like', $search.'%' )
      ->count();

      return [
          "draw" => $draw,
          "recordsTotal" => $division_count,
          "recordsFiltered" => $division_count,
          "data" => $division_list
      ];
    }

}
