<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Country;

class CountryController extends Controller
{
 public function index(){
   $country = Country::all();
   return view('country.country')->with('org_country', $country);
 }

 public function insertCountry(Request $request)
 { 
  $country = new Country(); 
   if ($country->validate($request->all()))   
        {
          if($request->country_id > 0){
                $country = Country::find($request->country_code);
            }  
         $country->country_code=$request->country_code;
          $country->country_description=$request->country_description;
          $country->saveOrFail();
          //return response()->json($country);
          echo json_encode(array('Saved'));

        }
         else{            
            // failure, get errors
            $errors = $country->errors();
            print_r($errors);
        }  

        }

    
   /* public function get_currency_list(){
        $currenc_list = Currency::all();
        echo json_encode($currenc_list);
    }
    
    
    public function get_currency(Request $request){
        $cur_id = $request->cur_id;
        $currency = Currency::find($cur_id);
        echo json_encode($currency);
    }*/
    


      }
