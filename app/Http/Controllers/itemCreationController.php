<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Finance\Item\Category;
use App\Models\Finance\Item\ContentType;
use App\Models\Finance\Item\Composition;
use App\Models\Finance\Item\PropertyValueAssign;
use App\itemCreation;
use Illuminate\Http\Request;

class itemCreationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 25;

        if (!empty($keyword)) {
            $itemcreation = itemCreation::where('master_id', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $itemcreation = itemCreation::latest()->paginate($perPage);
        }
        
        $data = array(
          'categories' => Category::all()
        );

        //return view('item-creation.index', compact('itemcreation'));
        return view('item-creation.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('item-creation.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        
        $requestData = $request->all();
        
        itemCreation::create($requestData);

        //return redirect('item-creation')->with('flash_message', 'itemCreation added!');
        echo json_encode(array('status' => 'success'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $itemcreation = itemCreation::findOrFail($id);

        return view('item-creation.show', compact('itemcreation'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $itemcreation = itemCreation::findOrFail($id);

        return view('item-creation.edit', compact('itemcreation'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {
        
        $requestData = $request->all();
        
        $itemcreation = itemCreation::findOrFail($id);
        $itemcreation->update($requestData);

        return redirect('item-creation')->with('flash_message', 'itemCreation updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        itemCreation::destroy($id);

        return redirect('item-creation')->with('flash_message', 'itemCreation deleted!');
    }
    
    public function GetMainCategory(){   
        
        $mainCategory = Category::all()->pluck('category_id', 'category_name');
        
        return json_encode($mainCategory);
    }
    
    public function GetMainCategoryByCode(Request $request){
        
        $objMainCategory = new Category();
        
        $category_id = $request->categoryId;
        
        $mainCategory = $objMainCategory->where('category_id','=',$category_id)->get();
        
        return json_encode($mainCategory);
        
    }
    
    public function SaveContentType(Request $request){
        
        $content_type = new ContentType();
        $content_type->type_description = strtoupper($request->content_type);
        
        $content_type->saveOrFail();
        echo json_encode(array('status' => 'success'));         
        
    }
    
    public function LoadContentType(){
        
        $content_type = new ContentType();
        $objContentType = $content_type->get();
        
        echo json_encode($objContentType);
        
    }
    
    public function SaveCompositions(Request $request){
        $compositions_type = new Composition();
        $compositions_type->content_description = $request->comp_description;
        $compositions_type->saveOrFail();
        
        echo json_encode(array('status' => 'success'));
        
    }
    
    public function LoadCompositions(){
        $compositions_type = new Composition();
        $objCompositions = $compositions_type->get();
        
        echo json_encode($objCompositions);
    }
    
    public function SavePropertyValue(Request $request){
        
        $propertyValueAssign = new PropertyValueAssign();
        $propertyValueAssign->property_id = $request->propertyid;
        $propertyValueAssign->assign_value = $request->propertyValue;
        $propertyValueAssign->status = 1;
        $propertyValueAssign->saveOrFail();
        
        echo json_encode(array('status' => 'success'));
    }   
    
    public function LoadPropertyValues(Request $request){
        
        $propertyValues = new PropertyValueAssign();
        $objPropertyValue = $propertyValues->where('property_id','=',$request->property_id)->get();
        
        echo json_encode($objPropertyValue);
        
    }
    
    public function CheckItemExist(Request $request){
        
        $item_desc = $request->master_description;  
        $rowCount = itemCreation::where('master_description','=',$item_desc)->count();
        
        echo json_encode(array('recordscount' => $rowCount));
        
        
    }
}
