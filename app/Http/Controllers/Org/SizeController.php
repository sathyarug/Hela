<?php

namespace App\Http\Controllers\Org;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use App\Models\Org\Size;
use Exception;

class SizeController extends Controller
{
    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
    }

    //get Size list
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


    //create a Size
    public function store(Request $request)
    {
      $size = new Size();
      if($size->validate($request->all()))
      {
        $size->fill($request->all());
        $size->status = 1;
        $size->save();

        return response([ 'data' => [
          'message' => 'Size was saved successfully',
          'size' => $size
          ]
        ], Response::HTTP_CREATED );
      }
      else
      {
          $errors = $size->errors();// failure, get errors
          return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }


    //get a Size
    public function show($id)
    {
      $size = Size::find($id);
      if($size == null)
        throw new ModelNotFoundException("Requested size not found", 1);
      else
        return response([ 'data' => $size ]);
    }


    //update a Size
    public function update(Request $request, $id)
    {
      $size = Size::find($id);
      if($size->validate($request->all()))
      {
        $size->fill($request->except('size_name'));
        $size->save();

        return response([ 'data' => [
          'message' => 'Size was updated successfully',
          'size' => $size
        ]]);
      }
      else
      {
        $errors = $size->errors();// failure, get errors
        return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }


    //deactivate a Size
    public function destroy($id)
    {
      $size = Size::where('size_id', $id)->update(['status' => 0]);
      return response([
        'data' => [
          'message' => 'Size was deactivated successfully.',
          'size' => $size
        ]
      ] , Response::HTTP_NO_CONTENT);
    }


    //validate anything based on requirements
    public function validate_data(Request $request){
      $for = $request->for;
      if($for == 'duplicate')
      {
        return response($this->validate_duplicate_code($request->size_id , $request->size_name));
      }
    }


    //check Size code already exists
    private function validate_duplicate_code($id , $code)
    {
      $size = Size::where('size_name','=',$code)->first();
      if($size == null){
        return ['status' => 'success'];
      }
      else if($size->size_id == $id){
        return ['status' => 'success'];
      }
      else {
        return ['status' => 'error','message' => 'Size code already exists'];
      }
    }


    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
      $query = null;
      if($fields == null || $fields == '') {
        $query = Size::select('*');
      }
      else{
        $fields = explode(',', $fields);
        $query = Size::select($fields);
        if($active != null && $active != ''){
          $query->where([['status', '=', $active]]);
        }
      }
      return $query->get();
    }

    //search Size for autocomplete
    private function autocomplete_search($search)
  	{
      $active=1;
  		$size_lists = Size::select('size_id','size_name')
  		->where([['size_name', 'like', '%' . $search . '%']])
      ->where('status','=',$active) ->get();
  		return $size_lists;
  	}


    //get searched Sizes for datatable plugin format
    private function datatable_search($data)
    {
      $start = $data['start'];
      $length = $data['length'];
      $draw = $data['draw'];
      $search = $data['search']['value'];
      $order = $data['order'][0];
      $order_column = $data['columns'][$order['column']]['data'];
      $order_type = $order['dir'];

      $size_list = Size::select('*')
      ->where('size_name'  , 'like', $search.'%' )
      ->orderBy($order_column, $order_type)
      ->offset($start)->limit($length)->get();

      $size_count = Size::where('size_name'  , 'like', $search.'%' )
      ->count();

      return [
          "draw" => $draw,
          "recordsTotal" => $size_count,
          "recordsFiltered" => $size_count,
          "data" => $size_list
      ];
    }

}
