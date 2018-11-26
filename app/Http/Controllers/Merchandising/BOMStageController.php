<?php

namespace App\Http\Controllers\Merchandising;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use App\Models\Merchandising\BOMStage;
use Exception;

class BOMStageController extends Controller
{
    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
    }

    //get Feature list
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


    //create a BOMStage
    public function store(Request $request)
    {
      $bomstage = new BOMStage();
      if($bomstage->validate($request->all()))
      {
        $bomstage->fill($request->all());
        $bomstage->status = 1;
        $bomstage->save();

        return response([ 'data' => [
          'message' => 'BOM Stage was saved successfully',
          'bomstage' => $bomstage
          ]
        ], Response::HTTP_CREATED );
      }
      else
      {
          $errors = $bomstage->errors();// failure, get errors
          return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }


    //get a Feature
    public function show($id)
    {
      $bomstage = BOMStage::find($id);
      if($bomstage == null)
        throw new ModelNotFoundException("Requested BOM Stage not found", 1);
      else
        return response([ 'data' => $bomstage ]);
    }


    //update a Feature
    public function update(Request $request, $id)
    {
      $bomstage = BOMStage::find($id);
      if($bomstage->validate($request->all()))
      {
        $bomstage->fill($request->all());
        $bomstage->save();

        return response([ 'data' => [
          'message' => 'BOM Stage was updated successfully',
          'bomstage' => $bomstage
        ]]);
      }
      else
      {
        $errors = $bomstage->errors();// failure, get errors
        return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }


    //deactivate a Feature
    public function destroy($id)
    {
      $bomstage = BOMStage::where('bom_stage_id', $id)->update(['status' => 0]);
      return response([
        'data' => [
          'message' => 'BOM Stage was deactivated successfully.',
          'bomstage' => $bomstage
        ]
      ] , Response::HTTP_NO_CONTENT);
    }


    //validate anything based on requirements
    public function validate_data(Request $request){
      $for = $request->for;
      if($for == 'duplicate')
      {
        return response($this->validate_duplicate_code($request->bom_stage_id , $request->bom_stage_description));
      }
    }


    //check Feature code already exists
    private function validate_duplicate_code($id , $code)
    {
      $bomstage = BOMStage::where('bom_stage_description','=',$code)->first();
      if($bomstage == null){
        return ['status' => 'success'];
      }
      else if($bomstage->bom_stage_id == $id){
        return ['status' => 'success'];
      }
      else {
        return ['status' => 'error','message' => 'BOM Stage already exists'];
      }
    }


    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
      $query = null;
      if($fields == null || $fields == '') {
        $query = BOMStage::select('*');
      }
      else{
        $fields = explode(',', $fields);
        $query = BOMStage::select($fields);
        if($active != null && $active != ''){
          $query->where([['status', '=', $active]]);
        }
      }
      return $query->get();
    }

    //search Size for autocomplete
    private function autocomplete_search($search)
  	{
  		$bomstage_lists = BOMStage::select('bom_stage_id','bom_stage_description')
  		->where([['bom_stage_description', 'like', '%' . $search . '%']]) ->get();
  		return $bomstage_lists;
  	}


    //get searched Features for datatable plugin format
    private function datatable_search($data)
    {
      $start = $data['start'];
      $length = $data['length'];
      $draw = $data['draw'];
      $search = $data['search']['value'];
      $order = $data['order'][0];
      $order_column = $data['columns'][$order['column']]['data'];
      $order_type = $order['dir'];

      $bomstage_list = BOMStage::select('*')
      ->where('bom_stage_description'  , 'like', $search.'%' )
      ->orderBy($order_column, $order_type)
      ->offset($start)->limit($length)->get();

      $bomstage_count = BOMStage::where('bom_stage_description'  , 'like', $search.'%' )
      ->count();

      return [
          "draw" => $draw,
          "recordsTotal" => $bomstage_count,
          "recordsFiltered" => $bomstage_count,
          "data" => $bomstage_list
      ];
    }

}
