<?php

namespace App\Http\Controllers\Org;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Org\Customer;
use App\Models\Finance\Accounting\PaymentTerm;
use App\Currency;
use App\Http\Resources\CustomerResource;

class CustomerController extends Controller
{
    public function index(){
        return view('org.customer.customer');
    }
     public function loadData() {
        $customer_list = Customer::all();
        echo json_encode($customer_list);
    }

    public function checkCode(Request $request) {
        $count = Customer::where('customer_code', '=', $request->code)->count();

        if ($request->idcode > 0) {

            $user = Customer::where('customer_id', $request->idcode)->first();

            if ($user->customer_code == $request->code) {
                $msg = true;
            } else {

                $msg = 'Already exists. please try another one';
            }
        } else {

            if ($count == 1) {

                $msg = 'Already exists. please try another one';
            } else {

                $msg = true;
            }
        }
        echo json_encode($msg);
    }

    public function saveCustomer(Request $request) {
        $customer = new Customer();
        if ($customer->validate($request->all())) {
            if ($request->customer_hid > 0) {
                $customer = Customer::find($request->customer_hid);
                $customer->customer_code=$request->customer_code;
            } else {
                $customer->fill($request->all());
                $customer->status = 1;
                $customer->created_by = 1;
            }
            $customer = $customer->saveOrFail();
            // echo json_encode(array('Saved'));
            echo json_encode(array('status' => 'success', 'message' => 'Customer details saved successfully.'));
        } else {
            // failure, get errors
            $errors = $customer->errors();
            echo json_encode(array('status' => 'error', 'message' => $errors));
        }
    }

    public function edit(Request $request) {
        $section_id = $request->section_id;
        $section = Section::find($section_id);
        echo json_encode($section);
    }

    public function delete(Request $request) {
        $customer_id = $request->customer_id;
        //$source = Main_Source::find($source_id);
        //$source->delete();
        $customer = Customer::where('customer_id', $customer_id)->update(['status' => 0]);
        echo json_encode(array('delete'));
    }
    public function loadCurrency(Request $request){

		$search_c = $request->search;
  		//print_r($search_c);
		$currency_lists = Currency::select('currency_id','currency_code','currency_description')
		->where([['currency_description', 'like', '%' . $search_c . '%'],]) ->get();


		return response()->json(['items'=>$currency_lists]);
    		//return $select_source;

	}
        
        public function loadPayemntTerms(Request $request){

		$search_c = $request->search;
  		//print_r($search_c);
		$payement_term_lists = PaymentTerm::select('payment_term_id','payment_code','payment_description')
		->where([['payment_description', 'like', '%' . $search_c . '%'],]) ->get();


		return response()->json(['items'=>$payement_term_lists]);
    		//return $select_source;

	}

    public function loadCustomer(Request $request) {
//        print_r(Customer::where('customer_name', 'LIKE', '%'.$request->search.'%')->get());exit;
        try{
            echo json_encode(Customer::where('customer_name', 'LIKE', '%'.$request->search.'%')->get());
//            return CustomerResource::collection(Customer::where('customer_name', 'LIKE', '%'.$request->search.'%')->get() );
        }
        catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
//        $customer_list = Customer::all();
//        echo json_encode($customer_list);
    }
}
