<?php

namespace App\Http\Controllers\Merchandising\Item;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use App\Models\Merchandising\Item\Category;
use App\Models\Merchandising\Item\SubCategory;
//use App\Models\Merchandising\Item\ContentType;
use App\Models\Merchandising\Item\Composition;
use App\Models\Merchandising\Item\PropertyValueAssign;
use App\Models\Merchandising\BulkCostingDetails;
use App\Models\Merchandising\Item\Item;


class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */

    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['GetItemList', 'GetItemListBySubCategory','GetItemDetailsByCode']]);
    }

    public function index(Request $request)
    {
        $type = $request->type;
        if($type == 'datatable') {
          $data = $request->all();
          return response($this->datatable_search($data));
        }
        else if($type == 'auto')    {
          $search = $request->search;
          return response($this->autocomplete_search($search));
        }
        else if($type == 'handsontable') {
          $search = $request->search;
          $category = $request->category;
          return response([
            'data' => $this->handsontable_list($category, $search)
          ]);
        }
        else if($type == 'item_selector'){
          $search = $request->search_text;
          $search_type = $request->search_type;
          $category = $request->category;
          $sub_category = $request->sub_category;
          return response([
            'data' => $this->item_selector_list($search_type, $category, $sub_category, $search)
          ]);
        }
        else {
        /*  $active = $request->active;
          $fields = $request->fields;
          return response([
            'data' => $this->list($active , $fields)
          ]);*/
        }
    }


    /*public function create()
    {
        return view('item-creation.create');
    }*/


    public function store(Request $request)
    {
        $item = new Item();
        if($item->validate($request->all()))
        {
          if($this->is_item_exist($request->master_description)){
            return response([
              'data' => [
                'status' => 'error',
                'message' => 'Item already exists'
              ]
            ]);
          }
          else{
            $item->fill($request->all());
            $item->master_description = strtoupper($item->master_description);
            $item->status=1;
            $item->save();

            $property_data = $request->property_data;
            for($x = 0 ; $x < sizeof($property_data) ; $x++){
              DB::table('item_property_data')->insert([
                  'master_id' => $item->master_id,
                  'property_id' => $property_data[$x]['property_id'],
                  'property_value_id' => $property_data[$x]['selected_property_value_id'],
                  'other_data' => $property_data[$x]['selected_property_value_data'],
                  'other_data_type' => $property_data[$x]['other_data_type'],
              ]);
            }

            $uom_list = $request->uom;
            for($x = 0 ; $x < sizeof($uom_list) ; $x++){
              /*$item->uoms()->create([
                  'master_id' => $item->master_id,
                  'uom_id' => $uom_list[$x]['uom_id']
              ]);*/
              DB::table('item_uom')->insert([
                  'master_id' => $item->master_id,
                  'uom_id' => $uom_list[$x]['uom_id']
              ]);
            }

            return response([
              'data' => [
                'status' => 'success',
                'message' => 'Item saved successfully'
              ]
            ]);
          }
        }
        else {
            $errors = $item->errors();// failure, get errors
            return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }


    //search itemmaster for autocomplete
    private function autocomplete_search($search)
  	{
  		$master_lists = Item::select('master_id','master_description')
  		->where([['master_description', 'like', '%' . $search . '%'],]) ->get();
  		return $master_lists;
  	}

    public function validate_data(Request $request){

      /*$for = $request->for;

      if($for == 'duplicate')
      {
        //print_r( $request->all());
        return response($this->validate_duplicate_code($request->id , $request->customer_name,
        $request->product_silhouette_description,$request->size_name));
      }*/

    }

  /*  private validate_duplicate_code($request->id , $request->customer_name,
    $request->product_silhouette_description,$request->size_name){



    }*/



  /*  public function show($id)
    {*/
        /*$itemcreation = itemCreation::findOrFail($id);

        return view('item-creation.show', compact('itemcreation'));*/
  /*  }*/


    public function edit($id)
    {
        /*$itemcreation = itemCreation::findOrFail($id);
        return view('item-creation.edit', compact('itemcreation'));*/
    }


    public function update(Request $request, $id)
    {
        /*$requestData = $request->all();
        $itemcreation = itemCreation::findOrFail($id);
        $itemcreation->update($requestData);

        return redirect('item-creation')->with('flash_message', 'itemCreation updated!');*/
    }


     //deactivate a item
     public function destroy($id)
     {
        //to check the deleting item used in costing
        $bukDetails = BulkCostingDetails::where([['main_item','=',$id]])->first();
        if($bukDetails != null) {
          return response([
            'data' => [
              'status'=>'error',
              'message' => "Cannot delete item. It's already used in costing."
              ]
          ]);
        }
        else {
           $itemCreation = Item::where('master_id', $id)->update(['status' => 0]);
           return response([
             'data' => [
               'status'=>'1',
               'message' => 'Item Deactivated successfully.',
               'item' => $itemCreation
             ]
           ]);
        }
     }

    private function datatable_search($data)
    {
      $start = $data['start'];
      $length = $data['length'];
      $draw = $data['draw'];
      $search = $data['search']['value'];
      $order = $data['order'][0];
      $order_column = $data['columns'][$order['column']]['data'];
      $order_type = $order['dir'];

      $item_list = Item::select('item_master.*', 'item_category.category_name', 'item_subcategory.subcategory_name','item_subcategory.subcategory_code')
      ->join('item_subcategory', 'item_subcategory.subcategory_id', '=', 'item_master.subcategory_id')
      ->join('item_category', 'item_category.category_id', '=', 'item_subcategory.category_id')
      ->where('item_master.master_description'  , 'like', $search.'%' )
      ->orWhere('item_subcategory.subcategory_name'  , 'like', $search.'%' )
      ->orWhere('item_category.category_name'  , 'like', $search.'%' )
      ->orderBy($order_column, $order_type)
      ->offset($start)->limit($length)->get();

      $item_count = Item::join('item_subcategory', 'item_subcategory.subcategory_id', '=', 'item_master.subcategory_id')
      ->join('item_category', 'item_category.category_id', '=', 'item_subcategory.category_id')
      ->where('item_master.master_description'  , 'like', $search.'%' )
      ->orWhere('item_subcategory.subcategory_name'  , 'like', $search.'%' )
      ->orWhere('item_category.category_name'  , 'like', $search.'%' )
      ->count();

      return [
          "draw" => $draw,
          "recordsTotal" => $item_count,
          "recordsFiltered" => $item_count,
          "data" => $item_list
      ];
    }

    /*public function GetMainCategory(){

        $mainCategory = Category::all()->pluck('category_id', 'category_name');

        return json_encode($mainCategory);
    }*/

  /*  public function GetMainCategoryByCode(Request $request){

        $objMainCategory = new Category();

        $category_id = $request->categoryId;

        $mainCategory = $objMainCategory->where('category_id','=',$category_id)->get();

        return json_encode($mainCategory);

    }*/


    public function check_and_generate_item_description(Request $request){
      $item_data = $request->item_data;
      $property_data = $request->property_data;

      $category = Category::find($item_data['category_code']);
      $sub_category = SubCategory::find($item_data['sub_category_code']);
      $composition = Composition::find($item_data['fabric_composition']);

      $item_description =  '';//$category->category_code . '#' . $sub_category->subcategory_code;
      if($category->category_id == 1){ //is a fabric
        $item_description .= ' ' . $composition->content_description;
      }

      for($x = 0 ; $x < sizeof($property_data) ; $x++){
        $prop_value = $property_data[$x]['selected_property_value'];
        $other_data_type = $property_data[$x]['other_data_type'];
        $other_data = $property_data[$x]['selected_property_value_data'];
        if($other_data != ''){
          if($other_data_type == 'AFTER'){
            $item_description .= ' ' . $prop_value . ' ' . $other_data;
          }
          else if($other_data_type == 'BEFORE'){
            $item_description .= ' ' . $other_data. ' ' . $prop_value;
          }
        }
        else{
          $item_description .= ' ' . $prop_value;
        }
      }

      if($this->is_item_exist($item_description)){ //item already exists
        return response([
          'data' => [
              'status' => 'error',
              'message' => 'Item (' . $item_description. ') already exists',
              'item_description' => $item_description
            ]
        ]);
      }
      else{
        return response([
          'data' => [
            'status' => 'success',
            'item_description' => $item_description
          ]
        ]);
      }
    }


    public function create_inventory_items(Request $request){
      $items = $request->items;
      $parent_item = Item::find($request->item_data['parent_item_id']);
      $category = Category::find($parent_item->category_id);
      //echo json_encode($parent_item);die();
      $res_arr = [];
      foreach($items as $item) {
        $exists_item = Item::where('master_description', '=', $item['master_description'])->first();
        if($exists_item == null) {//not a duplicate
          $i = new Item();
          $i->category_id = $parent_item->category_id;
          $i->subcategory_id = $parent_item->subcategory_id;
          $i->master_code = $item['master_code'];
          $i->master_description = $item['master_description'];
          $i->parent_item_id = $parent_item->master_id;
          $i->inventory_uom = $request->item_data['inventory_uom']['uom_id'];
          $i->standard_price = $item['standard_price'];
          $i->supplier_id = ($request->item_data['supplier_id'] == null) ? null : $request->item_data['supplier_id']['supplier_id'];
          $i->color_wise = ($request->item_data['color_wise'] == true) ? 1 : 0;
          $i->size_wise = ($request->item_data['size_wise'] == true) ? 1 : 0;
          $i->color_id = $item['color_id'];
          $i->size_id = $item['size_id'];
          $i->article_no = '';
          $i->moq = $item['moq'];
          $i->mcq = $item['mcq'];
          $i->status = 1;
          $i->save();
          //generate item codes
          $i->master_code = $category->category_code . str_pad($i->master_id, 7, '0', STR_PAD_LEFT);
          $i->save();

          $item['master_id'] = $i->master_id;
          $item['master_code'] = $i->master_code;
          $item['save_status'] = 'SAVE';
        }
        else {//already exists item
          $item['master_id'] = $exists_item->master_id;
          $item['master_code'] = $exists_item->master_code;
          $item['save_status'] = 'EXISTS';
        }
        array_push($res_arr, $item);
      }
      return response(['data' => [
        'status' => 'success',
        'message' => 'Item created successfully',
        'items' => $res_arr
        ]]);
    }


  /*  public function SaveContentType(Request $request){

        $content_type = new ContentType();
        $content_name = strtoupper($request->content_type);
        $status = "";

        if(ContentType::where('type_description','=',$content_name)->count()>0){
            $status = "exist";
        }else{
            $content_type->type_description = $content_name;

            $content_type->saveOrFail();
            $status = "success";
        }
        echo json_encode(array('status' => $status));

    }*/

  /*  public function LoadContentType(){

        $content_type = new ContentType();
        $objContentType = $content_type->get();

        echo json_encode($objContentType);

    }*/

    /*public function SaveCompositions(Request $request){
        $compositions_type = new Composition();
        $compositions_type->content_description = $request->comp_description;
        $compositions_type->saveOrFail();

        echo json_encode(array('status' => 'success'));

    }*/

    /*public function SavePropertyValue(Request $request){

        $propertyValueAssign = new PropertyValueAssign();
        $status = '';

        if($propertyValueAssign::where('property_id','=',$request->propertyid)->where('assign_value','=',$request->propertyValue)->count()>0){
            $status = 'exist';
        }else{
            $propertyValueAssign->property_id = $request->propertyid;
            $propertyValueAssign->assign_value = $request->propertyValue;
            $propertyValueAssign->status = 1;
            $propertyValueAssign->saveOrFail();

            $status = 'success';
        }




        echo json_encode(array('status' => $status));
    }*/



    private function handsontable_list($category, $search){
      $list = Item::join('item_subcategory', 'item_subcategory.subcategory_id', '=', 'item_master.subcategory_id')
      ->join('item_category', 'item_category.category_id', '=', 'item_subcategory.category_id')
      ->where('item_category.category_name', '=', $category)
      ->where('item_master.master_description', 'like', '%' . $search . '%')->get()->pluck('master_description');
      return $list;
    }


    private function item_selector_list($search_type, $category, $sub_category, $search){
      $list = Item::select('item_master.*', 'item_category.category_name','item_category.category_code', 'item_subcategory.subcategory_name', 'item_subcategory.subcategory_code')
      ->join('item_subcategory', 'item_subcategory.subcategory_id', '=', 'item_master.subcategory_id')
      ->join('item_category', 'item_category.category_id', '=', 'item_subcategory.category_id')
      ->where('item_master.master_description', 'like', '%' . $search . '%');

      if($search_type == 'MATERIAL_ITEMS'){
        $list = $list->whereNull('master_code');
      }
      else if($search_type == 'INVENTORY_ITEMS'){
        $list = $list->whereNotNull('master_code');
      }

      if($category != null && $category != ''){
        $list = $list->where('item_master.category_id', '=', $category);
      }
      if($sub_category != null && $sub_category != ''){
        $list = $list->where('item_master.subcategory_id', '=', $sub_category);
      }

      $result = $list->get();
      return $result;
    }


    private function is_item_exist($item_description){
        $rowCount = Item::where('master_description', '=', $item_description)->count();
        if($rowCount > 0)
          return true;
        else
          return false;
    }



  /*  public function GetItemList(Request $data){

      $start = $data['start'];
      $length = $data['length'];
      $draw = $data['draw'];
      $search = $data['search']['value'];
      $order = $data['order'][0];
      $order_column = $data['columns'][$order['column']]['data'];
      $order_type = $order['dir'];

      $itemCreationModel = new itemCreation();
      $rsItemList = $itemCreationModel->LoadItems();

      $countItems = $itemCreationModel->LoadItems()->count();

      //echo json_encode($rsItemList);

      return[
        "draw" => $draw,
        "recordsTotal" => $countItems,
        "recordsFiltered" => $countItems,
        "data" => $rsItemList

      ];

    }*/

    /*public function GetItemListBySubCategory(Request $request){

        $subCategoryCode = $request->subcatcode;
        $StyleItemList = itemCreation::where('subcategory_id','=',$subCategoryCode)->get();
        echo json_encode($StyleItemList);

    }*/

    /*public function GetItemDetailsByCode(Request $request){

        $ItemDetails = itemCreation::where('master_id','=',$request->item_code)->get();
        echo json_encode($ItemDetails);
    }*/
}
