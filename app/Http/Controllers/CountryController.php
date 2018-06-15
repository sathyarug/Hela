<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Country;
use Mockery\CountValidator\Exception;

class CountryController extends Controller
{
    public function index()
    {
        return view('country.country');
    }

    public function insertCountry(Request $request)
    {
        try {
            
            $country = new Country();
            if($request->country_id){
              $country=  Country::find($request->country_id);  
            }
            $country->country_code = $request->country_code;
            $country->country_description = $request->country_description;
            $country->save();
            return 'true';
        } catch (Exception $e) {
            return 'false';

        }
    }

    public function show()
    {
        try {
            return Country::get();
        } catch (Exception $e) {
            return 'false';
        }


    }
        public function delete($id){
        try{
            Country::destroy($id);
            return 'true';
        } catch(Exception $e){
            return 'false';
        }
    }

    public function edit($id){
        try{
            return Country::find($id);
        }catch (Exception $e){
            return 'false';

        }

    }
    public function update(Request $request,$id){
        try{
            $country =Country::find($id);
            $country->country_code = $request->country_code;
            $country->country_description = $request->country_description;
            $country->update();
            return 'true';


        }catch (Exception $e){
            return 'false';

        }
    }

}
