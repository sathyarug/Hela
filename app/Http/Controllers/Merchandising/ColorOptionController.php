<?php

namespace App\Http\Controllers\Merchandising;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use App\Models\Merchandising\ColorOption;
use Exception;

class ColorOptionController extends Controller
{
    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
    }

    //get Origin Type list
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


    //create a Origin Type
    public function store(Request $request)
    {
      $colorOption = new ColorOption;
      if($colorOption->validate($request->all()))
      {
        $colorOption->fill($request->all());
        $colorOption->status = 1;
        $colorOption->save();

        return response([ 'data' => [
          'message' => 'Color Option was saved successfully',
          'originType' => $colorOption
          ]
        ], Response::HTTP_CREATED );
      }
      else
      {
          $errors = $colorOption->errors();// failure, get errors
          return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }


    //get a Origin Type
    public function show($id)
    {
      $colorOption = ColorOption::find($id);
      if($colorOption == null)
        throw new ModelNotFoundException("Requested Color Option not found", 1);
      else
        return response([ 'data' => $colorOption ]);
    }


    //update a Origin Type
    public function update(Request $request, $id)
    {
      $colorOption = ColorOption::find($id);
      if($colorOption->validate($request->all()))
      {
        $colorOption->fill($request->all());
        $colorOption->save();

        return response([ 'data' => [
          'message' => 'Color Option was updated successfully',
          'colorOption' => $colorOption
        ]]);
      }
      else
      {
        $errors = $colorOption->errors();// failure, get errors
        return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }


    //deactivate a Origin Type
    public function destroy($id)
    {
      $colorOption = ColorOption::where('col_opt_id', $id)->update(['status' => 0]);
      return response([
        'data' => [
          'message' => 'Color Option was deactivated successfully.',
          'colorOption' => $colorOption
        ]
      ] , Response::HTTP_NO_CONTENT);
    }


    //validate anything based on requirements
    public function validate_data(Request $request){
      $for = $request->for;
      if($for == 'duplicate')
      {
        return response($this->validate_duplicate_code($request->col_opt_id , $request->color_option));
      }
    }


    //check OriginType code already exists
    private function validate_duplicate_code($id , $code)
    {
      $colorOption = ColorOption::where('color_option','=',$code)->first();
      if($colorOption == null){
        return ['status' => 'success'];
      }
      else if($colorOption->col_opt_id == $id){
        return ['status' => 'success'];
      }
      else {
        return ['status' => 'error','message' => 'Color Option already exists'];
      }
    }


    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
      $query = null;
      if($fields == null || $fields == '') {
        $query = ColorOption::select('*');
      }
      else{
        $fields = explode(',', $fields);
        $query =ColorOption::select($fields);
        if($active != null && $active != ''){
          $query->where([['status', '=', $active]]);
        }
      }
      return $query->get();
    }

    //search Origin Type for autocomplete
    private function autocomplete_search($search)
  	{
  		$color_option_lists = ColorOption::select('col_opt_id','color_option')
  		->where([['color_option', 'like', '%' . $search . '%'],]) ->get();
  		return $color_option_lists;
  	}


    //get searched OriginTypes for datatable plugin format
    private function datatable_search($data)
    {
      $start = $data['start'];
      $length = $data['length'];
      $draw = $data['draw'];
      $search = $data['search']['value'];
      $order = $data['order'][0];
      $order_column = $data['columns'][$order['column']]['data'];
      $order_type = $order['dir'];

      $color_option_lists =ColorOption::select('*')
      ->where('color_option'  , 'like', $search.'%' )
      ->orderBy($order_column, $order_type)
      ->offset($start)->limit($length)->get();

    $color_option_count = ColorOption::where('color_option'  , 'like', $search.'%' )->count();

      return [
          "draw" => $draw,
          "recordsTotal" => $color_option_count,
          "recordsFiltered" =>$color_option_count,
          "data" => $color_option_lists
      ];
    }

}
