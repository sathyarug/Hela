<?php

namespace App\Http\Controllers\Finance\Accounting;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Finance\Accounting\PaymentTerm;

class PaymentTermController extends Controller
{
    public function new_payment_term(){
        return view('org.payment_term.payment_term');
    }

    public function save(Request $request){
        $payment_term = new PaymentTerm();
        if ($payment_term->validate($request->all()))
        {
            if($request->payment_id > 0){
                $payment_term = PaymentTerm::find($request->payment_id);
                $payment_term->payment_description = $request->payment_description;
            }
            else{
              $payment_term->fill($request->all());
              $payment_term->created_by = 1;
            }
            $result = $payment_term->saveOrFail();
            echo json_encode(array('status' => 'success' , 'message' => 'Payment term details saved successfully.'));
        }
        else
        {
            // failure, get errors
            $errors = $payment_term->errors_tostring();
            echo json_encode(array('status' => 'error' , 'message' => $errors));
        }

    }

    public function get_list(Request $request){
      $data = $request->all();
  		$start = $data['start'];
  		$length = $data['length'];
  		$draw = $data['draw'];
  		$search = $data['search']['value'];
  		$order = $data['order'][0];
  		$order_column = $data['columns'][$order['column']]['data'];
  		$order_type = $order['dir'];

      $payment_term_list = PaymentTerm::select('*')
      ->where('payment_code','like',$search.'%')
      ->orWhere('payment_description','like',$search.'%')
      ->orderBy($order_column, $order_type)
  		->offset($start)->limit($length)->get();

      $payment_term_count = PaymentTerm::where('payment_code','like',$search.'%')
      ->orWhere('payment_description','like',$search.'%')
      ->count();

      echo json_encode(array(
  				"draw" => $draw,
  				"recordsTotal" => $payment_term_count,
  				"recordsFiltered" => $payment_term_count,
  				"data" => $payment_term_list
  		));
    }


    public function get_payment_term(Request $request){
        $payment_term_id = $request->payment_term_id;
        $payment_term = PaymentTerm::find($payment_term_id);
        echo json_encode($payment_term);
    }


    public function check_code(Request $request)
  	{
  		$payment_term = PaymentTerm::where('payment_code','=',$request->payment_code)->first();
  		if($payment_term == null){
  			echo json_encode(array('status' => 'success'));
  		}
  		else if($payment_term->payment_term_id == $request->payment_term_id){
  			echo json_encode(array('status' => 'success'));
  		}
  		else {
  			echo json_encode(array('status' => 'error','message' => 'Payment code already exists'));
  		}
  	}


      public function change_status(Request $request){
        $payment_term = PaymentTerm::find($request->payment_term_id);
        $payment_term->status = $request->status;
        $result = $payment_term->saveOrFail();
        echo json_encode(array('status' => 'success'));
    }

}

?>
