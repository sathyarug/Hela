<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Division;

class DivisionController extends Controller {

    public function index() {
        return view('division.division');
    }

    //check duplicate
    public function loadData() {
        $division_list = Division::all();
        echo json_encode($division_list);
    }

    public function checkCode(Request $request) {
        $count = Division::where('division_code', '=', $request->code)->count();

        if ($request->idcode > 0) {

            $user = Division::where('division_id', $request->idcode)->first();

            if ($user->division_code == $request->code) {
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

    public function saveDivision(Request $request) {
        $division = new Division();
        if ($division->validate($request->all())) {
            if ($request->division_hid > 0) {
                $division = Division::find($request->division_hid);
            }
            $division->fill($request->all());
            $division->status = 1;
            $division->created_by = 1;
            $division = $division->saveOrFail();
            // echo json_encode(array('Saved'));
            echo json_encode(array('status' => 'success', 'message' => 'Source details saved successfully.'));
        } else {
            // failure, get errors
            $errors = $division->errors();
            echo json_encode(array('status' => 'error', 'message' => $errors));
        }
    }

        public function edit(Request $request) {
        $division_id = $request->division_id;
        $division= Division::find($division_id);
        echo json_encode($division);
    }

    public function delete(Request $request) {
        $division_id = $request->division_id;
        //$source = Main_Source::find($source_id);
        //$source->delete();
        $division= Division::where('division_id', $division_id)->update(['status' => 0]);
        echo json_encode(array('delete'));
    }

}
