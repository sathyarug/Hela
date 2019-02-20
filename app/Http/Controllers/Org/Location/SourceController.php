<?php

namespace App\Http\Controllers\Org\Location;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use App\Models\Org\Location\Source;

class SourceController extends Controller
{
    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
    }

    //get Source list
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
      else{
        $active = $request->active;
        $fields = $request->fields;
        return response([
          'data' => $this->list($active , $fields)
        ]);
      }
    }


    //create a Source
    public function store(Request $request)
    {
      $source = new Source();
      if($source->validate($request->all()))
      {
        $source->fill($request->all());
        $source->status = 1;
        $source->save();

        return response([ 'data' => [
          'message' => 'Source was saved successfully',
          'source' => $source
          ]
        ], Response::HTTP_CREATED );
      }
      else
      {
          $errors = $source->errors();// failure, get errors
          return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }


    //get a Source
    public function show($id)
    {
      $source = Source::find($id);
      if($source == null)
        throw new ModelNotFoundException("Requested source not found", 1);
      else
        return response([ 'data' => $source ]);
    }


    //update a Source
    public function update(Request $request, $id)
    {
      $source = Source::find($id);
      if($source->validate($request->all()))
      {
        $source->fill($request->except('source_code'));
        $source->save();

        return response([ 'data' => [
          'message' => 'Source was updated successfully',
          'source' => $source
        ]]);
      }
      else
      {
        $errors = $source->errors();// failure, get errors
        return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }


    //deactivate a Source
    public function destroy($id)
    {
      $source = Source::where('source_id', $id)->update(['status' => 0]);
      return response([
        'data' => [
          'message' => 'Source was deactivated successfully.',
          'source' => $source
        ]
      ] , Response::HTTP_NO_CONTENT);
    }


    //validate anything based on requirements
    public function validate_data(Request $request){
      $for = $request->for;
      if($for == 'duplicate')
      {
        return response($this->validate_duplicate_code($request->source_id , $request->source_code));
      }
    }


    //check Source code already exists
    private function validate_duplicate_code($id , $code)
    {
      $source = Source::where('source_code','=',$code)->first();
      if($source == null){
        return ['status' => 'success'];
      }
      else if($source->source_id == $id){
        return ['status' => 'success'];
      }
      else {
        return ['status' => 'error','message' => 'Source code already exists'];
      }
    }


    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
      $query = null;
      if($fields == null || $fields == '') {
        $query = Source::select('*');
      }
      else{
        $fields = explode(',', $fields);
        $query = Source::select($fields);
        if($active != null && $active != ''){
          $query->where([['status', '=', $active]]);
        }
      }
      return $query->get();
    }


    //search Source for autocomplete
    private function autocomplete_search($search)
  	{
  		$source_lists = Source::select('source_id','source_name')
  		->where([['source_name', 'like', '%' . $search . '%'],]) ->get();
  		return $source_lists;
  	}


    //get searched Sources for datatable plugin format
    private function datatable_search($data)
    {
      $start = $data['start'];
      $length = $data['length'];
      $draw = $data['draw'];
      $search = $data['search']['value'];
      $order = $data['order'][0];
      $order_column = $data['columns'][$order['column']]['data'];
      $order_type = $order['dir'];

      $source_list = Source::select('*')
      ->where('source_code'  , 'like', $search.'%' )
      ->orWhere('source_name'  , 'like', $search.'%' )
      ->orWhere('source_name'  , 'like', $search.'%' )
      ->orderBy($order_column, $order_type)
      ->offset($start)->limit($length)->get();

      $source_count = Source::where('source_code'  , 'like', $search.'%' )
      ->orWhere('source_name'  , 'like', $search.'%' )
      ->orWhere('source_name'  , 'like', $search.'%' )
      ->count();

      return [
          "draw" => $draw,
          "recordsTotal" => $source_count,
          "recordsFiltered" => $source_count,
          "data" => $source_list
      ];
    }

}
