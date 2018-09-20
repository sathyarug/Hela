<?php

namespace App\Http\Controllers\Finance\Accounting;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use App\Models\Finance\Accounting\PaymentMethod;

class PaymentMethodController extends Controller
{
    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
    }

    //get payment method list
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

    //create a payment method
    public function store(Request $request)
    {
        $paymentMethod = new PaymentMethod();
        $paymentMethod->fill($request->all());
        $paymentMethod->status = 1;
        $paymentMethod->save();

        return response([ 'data' => [
          'message' => 'Payment Method was saved successfully',
          'paymentMethod' => $paymentMethod
          ]
        ], Response::HTTP_CREATED );
    }

    //get a payment method
    public function show($id)
    {
        $paymentMethod = PaymentMethod::find($id);
        if($paymentMethod == null)
          throw new ModelNotFoundException("Requested payment method not found", 1);
        else
          return response( ['data' => $paymentMethod] );
    }


    //update a payment method
    public function update(Request $request, $id)
    {
        $paymentMethod = PaymentMethod::find($id);
        $paymentMethod->fill( $request->except('payment_method_code'));
        $paymentMethod->save();

        return response([ 'data' => [
          'message' => 'Payment method was updated successfully',
          'paymentMethod' => $paymentMethod
        ]]);
    }

    //deactivate a payment method
    public function destroy($id)
    {
        $paymentMethod = PaymentMethod::where('payment_method_id', $id)->update(['status' => 0]);
        return response([
          'data' => [
            'message' => 'Payment method was deactivated successfully.',
            'paymentMethod' => $paymentMethod
          ]
        ] , Response::HTTP_NO_CONTENT);
    }


    //validate anything based on requirements
    public function validate_data(Request $request){
      $for = $request->for;
      if($for == 'duplicate')
      {
        return response($this->validate_duplicate_code($request->payment_method_id , $request->payment_method_code));
      }
    }


    //check payment method code already exists
    private function validate_duplicate_code($id , $code)
    {
      $paymentMethod = PaymentMethod::where('payment_method_code','=',$code)->first();
      if($paymentMethod == null){
        return ['status' => 'success'];
      }
      else if($paymentMethod->payment_method_id == $id){
        return ['status' => 'success'];
      }
      else {
        return ['status' => 'error','message' => 'Payment method code already exists'];
      }
    }


    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
      $query = null;
      if($fields == null || $fields == '') {
        $query = PaymentMethod::select('*');
      }
      else{
        $fields = explode(',', $fields);
        $query = PaymentMethod::select($fields);
        if($active != null && $active != ''){
          $query->where([['status', '=', $active]]);
        }
      }
      return $query->get();
    }


    //search payment methods for autocomplete
    private function autocomplete_search($search)
  	{
  		$payment_method_list = PaymentMethod::select('payment_method_id','payment_method_code')
  		->where([['payment_method_code', 'like', '%' . $search . '%'],]) ->get();
  		return $payment_method_list;
  	}


    //get searched payment methods for datatable plugin format
    private function datatable_search($data)
    {
      $start = $data['start'];
      $length = $data['length'];
      $draw = $data['draw'];
      $search = $data['search']['value'];
      $order = $data['order'][0];
      $order_column = $data['columns'][$order['column']]['data'];
      $order_type = $order['dir'];

      $payment_method_list = PaymentMethod::select('*')
      ->where('payment_method_code'  , 'like', $search.'%' )
      ->orWhere('payment_method_description'  , 'like', $search.'%' )
      ->orderBy($order_column, $order_type)
      ->offset($start)->limit($length)->get();

      $payment_method_count = PaymentMethod::where('payment_method_code'  , 'like', $search.'%' )
      ->orWhere('payment_method_description'  , 'like', $search.'%' )
      ->count();

      return [
          "draw" => $draw,
          "recordsTotal" => $payment_method_count,
          "recordsFiltered" => $payment_method_count,
          "data" => $payment_method_list
      ];
    }

}
