<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Currency;

class CurrencyController extends Controller
{
    public function new_currency(/*Request $request*/){
       
        
      /*  $data = array(
            'currency_code' => 'CUR',
            'currency_description' => '',
            'currency_id' => 10
        );//Input::all();

        // create a new model instance
        $b = new Currency($data);

        // attempt validation
        if ($b->validate($data))
        {
            print_r('success');
        }
        else
        {
            // failure, get errors
            $errors = $b->errors();
            print_r($errors);
        }*/
        
        
        return view('org.currency.currency');
    }
    
    public function save(Request $request){ 
        
        $currency = new Currency();       
        if ($currency->validate($request->all()))   
        {
            if($request->currency_id > 0){
                $currency = Currency::find($request->currency_id);
            }     
            $currency->fill($request->all());           
            $currency->created_by = 1;            
            $result = $currency->saveOrFail();
            echo json_encode(array('status' => 'success' , 'message' => 'Currency details saved successfully.'));
        }
        else
        {            
            // failure, get errors
            $errors = $currency->errors_tostring();
            echo json_encode(array('status' => 'error' , 'message' => $errors));
        }        
        
    }
    
    public function get_currency_list(){
        $currenc_list = Currency::all();
        echo json_encode($currenc_list);
    }
    
    
    public function get_currency(Request $request){
        $cur_id = $request->cur_id;
        $currency = Currency::find($cur_id);
        echo json_encode($currency);
    }
    
}

?>
