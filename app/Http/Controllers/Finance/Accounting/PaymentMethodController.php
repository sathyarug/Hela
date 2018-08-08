<?php

namespace App\Http\Controllers\Finance\Accounting;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Finance\Accounting\PaymentMethod;

class PaymentMethodController extends Controller
{
    /*public function new_payment_term(){
        return view('org.payment_term.payment_term');
    }*/

    public function save(Request $request){
        $payment_method = new PaymentMethod();
        if ($payment_method->validate($request->all()))
        {
            if($request->payment_method_id > 0){
                $payment_method = PaymentMethod::find($request->payment_method_id);
                $payment_method->payment_method_description = $request->payment_method_description;
            }
            else{
              $payment_method->fill($request->all());
              $payment_method->created_by = 1;
            }
            $result = $payment_method->saveOrFail();
            echo json_encode(array('status' => 'success' , 'message' => 'Payment method details saved successfully.'));
        }
        else
        {
            // failure, get errors
            $errors = $payment_method->errors_tostring();
            echo json_encode(array('status' => 'error' , 'message' => $errors));
        }

    }

    public function get_list(Request $request)
    {
      $data = $request->all();
  		$start = $data['start'];
  		$length = $data['length'];
  		$draw = $data['draw'];
  		$search = $data['search']['value'];
  		$order = $data['order'][0];
  		$order_column = $data['columns'][$order['column']]['data'];
  		$order_type = $order['dir'];

      $payment_method_list = PaymentMethod::select('*')
      ->where('payment_method_code','like',$search.'%')
  		->orWhere('payment_method_description', 'like', $search.'%')
      ->orderBy($order_column, $order_type)
  		->offset($start)->limit($length)->get();

      $payment_method_count = PaymentMethod::where('payment_method_code','like',$search.'%')
      ->orWhere('payment_method_description', 'like', $search.'%')
      ->count();

      echo json_encode(array(
  				"draw" => $draw,
  				"recordsTotal" => $payment_method_count,
  				"recordsFiltered" => $payment_method_count,
  				"data" => $payment_method_list
  		));
    }


    public function get_payment_method(Request $request){
        $payment_method_id = $request->payment_method_id;
        $payment_method = PaymentMethod::find($payment_method_id);
        echo json_encode($payment_method);
    }


    public function check_code(Request $request)
  	{
  		$payment_method = PaymentMethod::where('payment_method_code','=',$request->payment_method_code)->first();
  		if($payment_method == null){
  			echo json_encode(array('status' => 'success'));
  		}
  		else if($payment_method->payment_method_id == $request->payment_method_id){
  			echo json_encode(array('status' => 'success'));
  		}
  		else {
  			echo json_encode(array('status' => 'error','message' => 'payment method code already exists'));
  		}
  	}


      public function change_status(Request $request){
        $payment_method = PaymentMethod::find($request->payment_method_id);
        $payment_method->status = $request->status;
        $result = $payment_method->saveOrFail();
        echo json_encode(array('status' => 'success'));
    }

}

?>
