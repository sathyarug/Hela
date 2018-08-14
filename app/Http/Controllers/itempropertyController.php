<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\itemproperty;
use Illuminate\Http\Request;

use App\Models\Finance\Item\Category;
use App\Models\Finance\Item\SubCategory;
use App\assign_property;

class itempropertyController extends Controller
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
            $itemproperty = itemproperty::where('property_id', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $itemproperty = itemproperty::latest()->paginate($perPage);
        }
        
        $data = array(
          'categories' => Category::all()
        );

        //return view('itemproperty.itemproperty', compact('itemproperty',$data));
        return view('itemproperty.itemproperty',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('itemproperty.itemproperty.create');
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
        
        itemproperty::create($requestData);

        return redirect('itemproperty')->with('flash_message', 'itemproperty added!');
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
        $itemproperty = itemproperty::findOrFail($id);

        return view('itemproperty.itemproperty.show', compact('itemproperty'));
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
        $itemproperty = itemproperty::findOrFail($id);

        return view('itemproperty.itemproperty.edit', compact('itemproperty'));
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
        
        $itemproperty = itemproperty::findOrFail($id);
        $itemproperty->update($requestData);

        return redirect('itemproperty')->with('flash_message', 'itemproperty updated!');
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
    
    public function SavePropertyAssign(Request $request){
        
        $propperty_assign = new assign_property();
        
        $propperty_assign->property_id = $request->property_id;
        $propperty_assign->subcategory_id = $request->subcategory_code;
        $propperty_assign->status = 1;
        $propperty_assign->sequence_no = $request->sequence_no;
        
        $propperty_assign->saveOrFail();
        
        echo json_encode(array('status' => 'success'));        
    }
    
    public function LoadAssignProperties(Request $request){
       
        $propperty_assign = new itemproperty();
        $obj = $propperty_assign->LoadAssignProperties($request);
        
        echo json_encode($obj);
        
    }
    
    public function LoadUnAssignPropertiesBySubCat(){
        $objUnassignPropertiesBySubCat = itemproperty::LoadUnAssignPropertiesBySubCat();
        echo json_encode($objUnassignPropertiesBySubCat);
    }
}
