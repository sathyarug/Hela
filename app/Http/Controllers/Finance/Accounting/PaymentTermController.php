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

    public function get_payment_term_list(){
        $payment_term_list = PaymentTerm::all();
        echo json_encode($payment_term_list);
    }


    public function get_payment_term(Request $request){
        $payment_term_id = $request->payment_term_id;
        $payment_term = PaymentTerm::find($payment_term_id);
        echo json_encode($payment_term);
    }


    public function check_perment_term_code(Request $request)
    {
        $count = PaymentTerm::where('payment_code','=',$request->payment_code)->count();
        if($count >= 1){
              $msg = 'Payment code already exists';
          }else{
              $msg = true;
        }
        echo json_encode($msg);
    }


      public function change_status(Request $request){
        $payment_term = PaymentTerm::find($request->payment_term_id);
        $payment_term->status = $request->status;
        $result = $payment_term->saveOrFail();
        echo json_encode(array('status' => 'success'));
    }

}

?>
