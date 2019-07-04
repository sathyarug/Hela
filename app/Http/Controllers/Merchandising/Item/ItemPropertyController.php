<?php

namespace App\Http\Controllers\Merchandising\Item;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\Models\Merchandising\Item\ItemProperty;
use App\Models\Merchandising\Item\PropertyValueAssign;
use App\Models\Merchandising\Item\Category;
use App\Models\Merchandising\Item\SubCategory;
use App\Models\Merchandising\Item\AssignProperty;

class ItemPropertyController extends Controller
{

    public function index(Request $request)
    {
        $type = $request->type;

        if($type == 'assigned_properties'){
          $sub_category = $request->sub_category;
          return response([
            'data' => $this->load_assign_properties($sub_category)
          ]);
        }
        else if($type == 'property_values'){
          $property_id = $request->property_id;
          return response([
            'data' => $this->load_property_values($property_id)
          ]);
        }
        /*$keyword = $request->get('search');
        $perPage = 25;

        if (!empty($keyword)) {
            $itemproperty = itemproperty::where('property_id', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $itemproperty = itemproperty::latest()->paginate($perPage);
        }

        $data = array(
          'categories' => Category::all()
        );*/

        //return view('itemproperty.itemproperty', compact('itemproperty',$data));
      //  return view('itemproperty.itemproperty',$data);
    }


    public function create()
    {
      //  return view('itemproperty.itemproperty.create');
    }


    public function store(Request $request)
    {

        $requestData = $request->all();

        itemproperty::create($requestData);

        return redirect('itemproperty')->with('flash_message', 'itemproperty added!');
    }


    public function show($id)
    {
        $itemproperty = itemproperty::findOrFail($id);

        return view('itemproperty.itemproperty.show', compact('itemproperty'));
    }


    public function edit($id)
    {
        $itemproperty = itemproperty::findOrFail($id);

        return view('itemproperty.itemproperty.edit', compact('itemproperty'));
    }


    public function update(Request $request, $id)
    {

        $requestData = $request->all();

        $itemproperty = itemproperty::findOrFail($id);
        $itemproperty->update($requestData);

        return redirect('itemproperty')->with('flash_message', 'itemproperty updated!');
    }


    public function destroy($id)
    {
        itemproperty::destroy($id);

        return redirect('itemproperty')->with('flash_message', 'itemproperty deleted!');
    }

    public function SaveItemProperty(Request $request){

        $item_properties = new itemproperty();

        $item_properties->property_name = $request->property_name;
        $item_properties->status = 1;
        $item_properties->saveOrFail();

         echo json_encode(array('status' => 'success'));
    }

    public function LoadProperties(){

        $item_property = itemproperty::where('status','=','1')->pluck('property_id','property_name');

        echo json_encode($item_property);
    }

    public function RemoveAssign(Request $request){

        $propperty_assign = new assign_property();

        echo json_encode("Code : ".$request->sub_code);

        $propperty_assign::where('subcategory_id',$request->sub_code)->delete();


    }

    public function SavePropertyAssign(Request $request){

        $propperty_assign = new assign_property();


        $obj = assign_property::where('property_id',$request->property_id)->where('subcategory_id',$request->subcategory_code);

        if($obj->count()>0){
             $obj->sequence_no = $request->sequence_no;
             $obj->save();

        }else{

            $propperty_assign->property_id = $request->property_id;
            $propperty_assign->subcategory_id = $request->subcategory_code;
            $propperty_assign->status = 1;
            $propperty_assign->sequence_no = $request->sequence_no;

            $propperty_assign->saveOrFail();

        }

        echo json_encode(array('status' => 'success'));
    }


    private function load_assign_properties($sub_category){
        $propperty_assign = new ItemProperty();
        $arr = $propperty_assign->load_assign_properties($sub_category);
        for($x = 0 ; $x < sizeof($arr) ; $x++) {
          $arr[$x]->property_values = $this->load_property_values($arr[$x]->property_id);
          $arr[$x]->data1 = 0;
        }
        return $arr;
    }

    public function LoadUnAssignPropertiesBySubCat(Request $request){
        $propperty_assign = new itemproperty();

        $subcatcode = $request->subcategory_code;
        $objUnassignPropertiesBySubCat = $propperty_assign->LoadUnAssignPropertiesBySubCat($request);
        echo json_encode($objUnassignPropertiesBySubCat);
    }

    public function CheckProperty(Request $request){

        $property_name = $request->property_name;
        $recCount = itemproperty::where('property_name','=',$property_name)->count();

        echo json_encode(array('recordscount' => $recCount));


    }



    public function load_un_assign_list(Request $request){
      $subCatCode = $request->subCatCode;

      $subCat = DB::table('item_property')
      ->select('item_property.property_id','item_property.property_name')
      ->whereNotIn('item_property.property_id',function($q) use ($subCatCode){
         $q->select('property_id')->from('item_property_assign')->where('subcategory_id',$subCatCode);})
         ->get();

      /*$subCat = itemproperty::select('item_property_assign.*','item_property.*')
         ->join('item_property_assign','item_property_assign.property_id','=','item_property.property_id')
         ->where('item_property_assign.subcategory_id' , '=', $subCatCode )
         ->get();*/

         return response([ 'count' => sizeof($subCat), 'subCat'=> $subCat ]);


    }

    public function load_un_assign_list2(Request $request){
      $subCatCode2 = $request->subCatCode2;
      $subCat2 = itemproperty::select('item_property_assign.*','item_property.*')
         ->join('item_property_assign','item_property_assign.property_id','=','item_property.property_id')
         ->where('item_property_assign.subcategory_id' , '=', $subCatCode2 )
         ->get();

         return response([ 'count2' => sizeof($subCat2), 'subCat2'=> $subCat2 ]);

    }

    private function load_property_values($property_id){
        $list = PropertyValueAssign::where('property_id', '=', $property_id)->get();
        return $list;

    }

}
