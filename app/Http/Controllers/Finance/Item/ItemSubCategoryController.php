<?php

namespace App\Http\Controllers\Finance\Item;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Finance\Item\SubCategory;
use App\Models\Finance\Item\Category;
use Exception;
use App\Libraries\AppAuthorize;

class ItemSubCategoryController extends Controller
{
    var $authorize = null;

    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
      $this->authorize = new AppAuthorize();
    }

    public function new(){
        $data = array(
          'categories' => Category::all()
        );
        return view('finance.item.item',$data);
    }

    public function save(Request $request){
      if($this->authorize->hasPermission('SUB_CAT_MANAGE'))//check permission
      {
        //print_r($request->all());die();
          $sub_category = new SubCategory();
          if ($sub_category->validate($request->all()))
          {
              if($request->subcategory_id > 0){
                  $sub_category = SubCategory::find($request->subcategory_id);
                  $sub_category->category_id = $request->category_code;
                  $sub_category->subcategory_name = $request->subcategory_name;
                  $sub_category->is_inspectiion_allowed = $request->is_inspectiion_allowed;
                  $sub_category->is_display = $request->is_display;
              }
              else{
                $sub_category->fill($request->all());
                $sub_category->category_id = $request->category_code;
                $sub_category->is_inspectiion_allowed = $request->is_inspectiion_allowed;
                $sub_category->is_display = $request->is_display;
                $sub_category->status = 1;
                $sub_category->created_by = 1;
              }
              $result = $sub_category->saveOrFail();
              echo json_encode(array('status' => 'success' , 'message' => 'Sub category details saved successfully.'));
          }
          else
          {
              // failure, get errors
              $errors = $sub_category->errors_tostring();
              echo json_encode(array('status' => 'error' , 'message' => $errors));
          }
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }

    public function get_sub_category_list(){
        //$sub_category_list = SubCategory::all();
        $sub_category_list = SubCategory::GetSubCategoryList();
        echo json_encode($sub_category_list);
    }


    public function get(Request $request){
      if($this->authorize->hasPermission('SUB_CAT_MANAGE'))//check permission
      {
        $sub_category_id = $request->subcategory_id;
        $sub_category = SubCategory::select("*")->where("subcategory_id","=",$sub_category_id)->get();
        echo json_encode($sub_category);
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    public function check_sub_category_code(Request $request)
    {
        $count = SubCategory::where('subcategory_code','=',$request->subcategory_code)->count();
        if($count >= 1){
              //$msg = 'Sub category code already exists';
            $msg =array('status' => 'error','message' => 'Record already exists');
          }else{
        $msg = array('status' => 'success');
        }
        echo json_encode($msg);
    }


      public function change_status(Request $request){
        $sub_category = SubCategory::find($request->subcategory_id);
        $sub_category->status = $request->status;
        $result = $sub_category->saveOrFail();
        echo json_encode(array('status' => 'success'));
    }

    public function get_category_list(){
        $category_list = Category::all();
        echo json_encode($category_list);
    }

    public function get_subcat_list_by_maincat(Request $request){

        //$sub_category = SubCategory::where('category_id','=',$request->category_id)->pluck('subcategory_id', 'subcategory_name');
        //$sub_category = SubCategory::where('category_id','=',$request->category_id)->get();
        $sub_category = SubCategory::where('category_id','=',$request->category_id)->where('status','=','1')->get();
        echo json_encode($sub_category);
    }

    public function LoadSubCategoryList(Request $request)
    {
      if($this->authorize->hasPermission('SUB_CAT_MANAGE'))//check permission
      {
        $data = $request->all();
        //$start = $data['start'];
        $length = $data['length'];
        $draw = $data['draw'];
        $search = $data['search']['value'];
        $order = $data['order'][0];
        $order_column = $data['columns'][$order['column']]['data'];
        $order_type = $order['dir'];

        $sub_category_list = SubCategory::GetSubCategoryList();

        $subCategoryCount = SubCategory::GetSubCategoryCount();

        echo json_encode(array(
            "draw" => $draw,
            "recordsTotal" => $subCategoryCount,
            "recordsFiltered" => $subCategoryCount,
            "data" => $sub_category_list
        ));
        //echo json_encode($sub_category_list);
      }
      else{
        return response($this->authorize->error_response(), 401);
      }        
    }

}

?>
