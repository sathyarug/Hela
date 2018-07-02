<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Country;
use Mockery\CountValidator\Exception;

class CountryController extends Controller {

    public function index() {
        return view('country.country_page');
    }

    public function insertCountry(Request $request) {
        try {

            $country = new Country();
            if ($request->country_id) {
                $country = Country::find($request->country_id);
            }
            $country->country_code = $request->country_code;
            $country->country_description = $request->country_description;
            $country->save();
            return 'true';
        } catch (Exception $e) {
            return 'false';
        }
    }

    /* public function show() {
      try {
      return Country::get();
      } catch (Exception $e) {
      return 'false';
      }
      } */

    public function loaddata() {
        $source_list = Country::all();
        echo json_encode($source_list);
    }

    public function checkCode(Request $request) {
        $count = Country::where('country_code', '=', $request->code)->count();

        if ($request->idcode > 0) {

            $user = Country::where('country_id', $request->idcode)->first();

            if ($user->country_code == $request->code) {
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

    public function saveCountry(Request $request) {
        $country = new Country();
        if ($country->validate($request->all())) {
            if ($request->country_hid > 0) {
                $country = Country::find($request->country_hid);
            }
            $country->fill($request->all());
            $country->status = 1;
            $country->created_by = 1;
            $result = $country->saveOrFail();
            // echo json_encode(array('Saved'));
            echo json_encode(array('status' => 'success', 'message' => 'Source details saved successfully.'));
        } else {
            // failure, get errors
            $errors = $country->errors();
            echo json_encode(array('status' => 'error', 'message' => $errors));
        }
    }

    public function edit(Request $request) {
        $country_id = $request->country_id;
        $country = Country::find($country_id);
        echo json_encode($country);
    }

    public function delete(Request $request) {
        $country_id = $request->country_id;
        //$source = Main_Source::find($source_id);
        //$source->delete();
        $country = Country::where('country_id', $country_id)->update(['status' => 0]);
        echo json_encode(array('delete'));
    }

   /* public function delete($id) {
        try {
            Country::destroy($id);
            return 'true';
        } catch (Exception $e) {
            return 'false';
        }
    }*/

   /* public function edit($id) {
        try {
            return Country::find($id);
        } catch (Exception source_hid) {
            return 'false';
        }
    }*/

    public function update(Request $request, $id) {
        try {
            $country = Country::find($id);
            $country->country_code = $request->country_code;
            $country->country_description = $request->country_description;
            $country->update();
            return 'true';
        } catch (Exception $e) {
            return 'false';
        }
    }

}
