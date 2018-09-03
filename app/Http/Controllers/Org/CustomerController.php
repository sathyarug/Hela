<?php

namespace App\Http\Controllers\Org;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use App\Models\Org\Customer;

class CustomerController extends Controller
{
    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
    }

    //get customer list
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
        return response([]);
      }
    }


    //create a customer
    public function store(Request $request)
    {
      $customer = new Customer();
      if($customer->validate($request->all()))
      {
        $customer->fill($request->all());
        $customer->status = 1;
        $customer->save();

        return response([ 'data' => [
          'message' => 'Customer was saved successfully',
          'customer' => $customer
          ]
        ], Response::HTTP_CREATED );
      }
      else
      {
          $errors = $customer->errors();// failure, get errors
          return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }


    //get a customer
    public function show($id)
    {
      $customer = Customer::find($id);
      if($customer == null)
        throw new ModelNotFoundException("Requested customer not found", 1);
      else
        return response([ 'data' => $customer ]);
    }


    //update a customer
    public function update(Request $request, $id)
    {
      $customer = Customer::find($id);
      if($customer->validate($request->all()))
      {
        $customer->fill($request->except('customer_code'));
        $customer->save();

        return response([ 'data' => [
          'message' => 'Customer was updated successfully',
          'customer' => $customer
        ]]);
      }
      else
      {
        $errors = $customer->errors();// failure, get errors
        return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }


    //deactivate a customer
    public function destroy($id)
    {
      $customer = Customer::where('customer_id', $id)->update(['status' => 0]);
      return response([
        'data' => [
          'message' => 'Customer was deactivated successfully.',
          'customer' => $customer
        ]
      ] , Response::HTTP_NO_CONTENT);
    }


    //validate anything based on requirements
    public function validate_data(Request $request){
      $for = $request->for;
      if($for == 'duplicate')
      {
        return response($this->validate_duplicate_code($request->customer_id , $request->customer_code));
      }
    }


    //check customer code already exists
    private function validate_duplicate_code($id , $code)
    {
      $customer = Customer::where('customer_code','=',$code)->first();
      if($customer == null){
        return ['status' => 'success'];
      }
      else if($customer->customer_id == $id){
        return ['status' => 'success'];
      }
      else {
        return ['status' => 'error','message' => 'Customer code already exists'];
      }
    }


    //search customer for autocomplete
    private function autocomplete_search($search)
  	{
  		$customer_lists = Customer::select('customer_id','customer_name')
  		->where([['customer_name', 'like', '%' . $search . '%'],]) ->get();
  		return $customer_lists;
  	}


    //get searched customers for datatable plugin format
    private function datatable_search($data)
    {
      $start = $data['start'];
      $length = $data['length'];
      $draw = $data['draw'];
      $search = $data['search']['value'];
      $order = $data['order'][0];
      $order_column = $data['columns'][$order['column']]['data'];
      $order_type = $order['dir'];

      $customer_list = Customer::select('*')
      ->where('customer_code'  , 'like', $search.'%' )
      ->orWhere('customer_name'  , 'like', $search.'%' )
      ->orWhere('customer_short_name'  , 'like', $search.'%' )
      ->orderBy($order_column, $order_type)
      ->offset($start)->limit($length)->get();

      $customer_count = Customer::where('customer_code'  , 'like', $search.'%' )
      ->orWhere('customer_name'  , 'like', $search.'%' )
      ->orWhere('customer_short_name'  , 'like', $search.'%' )
      ->count();

      return [
          "draw" => $draw,
          "recordsTotal" => $customer_count,
          "recordsFiltered" => $customer_count,
          "data" => $customer_list
      ];
    }

}
