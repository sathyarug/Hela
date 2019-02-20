<?php

namespace App\Http\Controllers\Org;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use App\Models\Org\Supplier;

class SupplierController extends Controller
{
    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index','loadSuppliers']]);
    }

    //get Supplier list
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


    //create a Supplier
    public function store(Request $request)
    {
      $supplier = new Supplier();
      if($supplier->validate($request->all()))
      {
        $supplier->fill($request->all());
        $supplier->status = 1;
        $supplier->save();

        return response([ 'data' => [
          'message' => 'Supplier was saved successfully',
          'supplier' => $supplier
          ]
        ], Response::HTTP_CREATED );
      }
      else
      {
          $errors = $supplier->errors();// failure, get errors
          return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }


    //get a Supplier
    public function show($id)
    {
      $supplier = Supplier::with(['country','currency'])->find($id);
      if($supplier == null)
        throw new ModelNotFoundException("Requested supplier not found", 1);
      else
        return response([ 'data' => $supplier ]);
    }


    private function list($active = 0 , $fields = null)
    {
      $query = null;
      if($fields == null || $fields == '') {
        $query = Supplier::select('*');
      }
      else{
        $fields = explode(',', $fields);
        $query = Supplier::select($fields);
        if($active != null && $active != ''){
          $query->where([['status', '=', $active]]);
        }
      }
      return $query->get();
    }
    
    //update a Supplier
    public function update(Request $request, $id)
    {
      $supplier = Supplier::find($id);
      if($supplier->validate($request->all()))
      {
        $supplier->fill($request->except('supplier_code'));
        $supplier->save();

        return response([ 'data' => [
          'message' => 'Supplier was updated successfully',
          'supplier' => $supplier
        ]]);
      }
      else
      {
        $errors = $supplier->errors();// failure, get errors
        return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }


    //deactivate a Supplier
    public function destroy($id)
    {
      $supplier = Supplier::where('supplier_id', $id)->update(['status' => 0]);
      return response([
        'data' => [
          'message' => 'Supplier was deactivated successfully.',
          'supplier' => $supplier
        ]
      ] , Response::HTTP_NO_CONTENT);
    }


    //validate anything based on requirements
    public function validate_data(Request $request){
      $for = $request->for;
      if($for == 'duplicate')
      {
        return response($this->validate_duplicate_code($request->supplier_id , $request->supplier_code));
      }
    }


    //check Supplier code already exists
    private function validate_duplicate_code($id , $code)
    {
      $supplier = Supplier::where('supplier_code','=',$code)->first();
      if($supplier == null){
       echo json_encode(array('status' => 'success'));
      }
      else if($supplier->supplier_id == $id){
         echo json_encode(array('status' => 'success'));
      }
      else {
         echo json_encode(array('status' => 'error','message' => 'Supplier code already exists'));
      }
    }


    //search Supplier for autocomplete
    private function autocomplete_search($search)
  	{
  		$supplier_lists = Supplier::select('supplier_id','supplier_name')
  		->where([['supplier_name', 'like', '%' . $search . '%'],]) ->get();
  		return $supplier_lists;
  	}


    //get searched Suppliers for datatable plugin format
    private function datatable_search($data)
    {
      $start = $data['start'];
      $length = $data['length'];
      $draw = $data['draw'];
      $search = $data['search']['value'];
      $order = $data['order'][0];
      $order_column = $data['columns'][$order['column']]['data'];
      $order_type = $order['dir'];

      $supplier_list = Supplier::select('*')
      ->where('supplier_code'  , 'like', $search.'%' )
      ->orWhere('supplier_name'  , 'like', $search.'%' )
      ->orWhere('supplier_short_name'  , 'like', $search.'%' )
      ->orderBy($order_column, $order_type)
      ->offset($start)->limit($length)->get();

      $supplier_count = Supplier::where('supplier_code'  , 'like', $search.'%' )
      ->orWhere('supplier_name'  , 'like', $search.'%' )
      ->orWhere('supplier_short_name'  , 'like', $search.'%' )
      ->count();

      return [
          "draw" => $draw,
          "recordsTotal" => $supplier_count,
          "recordsFiltered" => $supplier_count,
          "data" => $supplier_list
      ];
    }

    public function loadSuppliers(Request $request){
        $search = $request->search;
        //$supplier_list = Supplier::all();
        $supplier_list = Supplier::select('supplier_id', 'supplier_code', 'supplier_name')
                ->where('supplier_name'  , 'like', $search.'%')->get();
        echo json_encode($supplier_list);
    }

}
