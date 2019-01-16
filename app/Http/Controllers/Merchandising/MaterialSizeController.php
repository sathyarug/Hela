<?php

namespace App\Http\Controllers\Merchandising;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use App\Models\Merchandising\MaterialSize;
use App\Models\Finance\Item\Category;
use App\Models\Finance\Item\SubCategory;

use Exception;

class MaterialSizeController extends Controller
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


    //create a Material Size
    public function store(Request $request)
    {
      $matsize = new MaterialSize();
      if($matsize->validate($request->all()))
      {
        $matsize->fill($request->all());
        $matsize->status = 1;
        $matsize->po_status = 0;
        $matsize->save();

        return response([ 'data' => [
          'message' => 'Material Size was saved successfully',
          'matsize' => $matsize
          ]
        ], Response::HTTP_CREATED );
      }
      else
      {
          $errors = $matsize->errors();// failure, get errors
          return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }


    //get a Feature
    public function show($id)
    {
      $matsize = MaterialSize::with(['category','subCategory'])->find($id);
      if($matsize == null)
        throw new ModelNotFoundException("Requested Material Size not found", 1);
      else
        return response([ 'data' => $matsize ]);
    }


    //update a Feature
    public function update(Request $request, $id)
    {
      $matsize = MaterialSize::find($id);
      if($matsize->validate($request->all()))
      {
        $matsize->fill($request->all());
        $matsize->save();

        return response([ 'data' => [
          'message' => 'Material Size was updated successfully',
          'matsize' => $matsize
        ]]);
      }
      else
      {
        $errors = $matsize->errors();// failure, get errors
        return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }


    //deactivate a Feature
    public function destroy($id)
    {
      $matsize = MaterialSize::where('mat_size_id', $id)->update(['status' => 0]);
      return response([
        'data' => [
          'message' => 'Material Size was deactivated successfully.',
          'matsize' => $matsize
        ]
      ] , Response::HTTP_NO_CONTENT);
    }


    //validate anything based on requirements
    public function validate_data(Request $request){
      $for = $request->for;
      if($for == 'duplicate')
      {
        return response($this->validate_duplicate_code($request->mat_size_id,$request->category_name,$request
        ->subcategory_name));
      }
    }


    //check Feature code already exists
    private function validate_duplicate_code($id, $mainCat, $subCat)
    {
      $matsize = MaterialSize :: where([['category_id','=',$mainCat],['subcategory_id','=',$subCat]])->first();
      if($matsize == null){
        echo json_encode(array('status' => 'success'));
      }
      else if($matsize->mat_size_id == $id){
        echo json_encode(array('status' => 'success'));
      }
      else {
        echo json_encode(array('status' => 'error','message' => 'Material Size already given'));
      }
    }


    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
      $query = null;
      if($fields == null || $fields == '') {
        $query = MaterialSize::select('*');
      }
      else{
        $fields = explode(',', $fields);
        $query = MaterialSize::select($fields);
        if($active != null && $active != ''){
          $query->where([['status', '=', $active]]);
        }
      }
      return $query->get();
    }

    //search Size for autocomplete
    private function autocomplete_search($search)
  	{
  		$matsize_lists = MaterialSize::join('item_category', 'merc_mat_size.category_id', '=' , 'item_category.category_id')
                      ->join('item_subcategory', 'merc_mat_size.subcategory_id', '=' , 'item_subcategory.subcategory_id')
                      ->select('merc_mat_size.*','item_subcategory.subcategory_name','item_category.category_name')
                      ->where([['item_category.category_name', 'like', '%' . $search . '%']]) ->get();
  	                   return $matsize_lists;
  	}

    public function get_sub_cat(Request $request){
        $category = $request->category_id;
        //$sub_category = SubCategory::where('category_id','=',$request->category_id)->pluck('subcategory_id', 'subcategory_name');
        $sub_category = SubCategory::join('item_category', 'item_subcategory.category_code', '=' , 'item_category.category_code')
                        ->select('item_subcategory.subcategory_name','item_subcategory.subcategory_id')
                        ->where('item_category.category_id','=', $category )
                        ->get();

        echo json_encode($sub_category);
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

      $matsize_list = MaterialSize::join('item_category', 'merc_mat_size.category_id', '=' , 'item_category.category_id')
                      ->join('item_subcategory', 'merc_mat_size.subcategory_id', '=' , 'item_subcategory.subcategory_id')
                      ->select('merc_mat_size.*','item_subcategory.subcategory_name','item_category.category_name')
                      ->where('item_category.category_name'  , 'like', $search.'%' )
                      ->orwhere('item_subcategory.subcategory_name', 'like', $search.'%')
                      ->orderBy($order_column, $order_type)
                      ->offset($start)->limit($length)->get();

      $matsize_count = MaterialSize::join('item_category', 'merc_mat_size.category_id', '=' , 'item_category.category_id')
                      ->join('item_subcategory', 'merc_mat_size.subcategory_id', '=' , 'item_subcategory.subcategory_id')
                      ->select('merc_mat_size.*','item_subcategory.subcategory_name','item_category.category_name')
                      ->where('item_category.category_name'  , 'like', $search.'%' )
                      ->count();

      return [
          "draw" => $draw,
          "recordsTotal" => $matsize_count,
          "recordsFiltered" => $matsize_count,
          "data" => $matsize_list
      ];
    }

}
