<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\UnitOfMeasure;

class UomController extends Controller {

    public function index() {
        return view('uom.uom');
    }

    public function loadData() {
        $uom_list = UnitOfMeasure::all();
        echo json_encode($uom_list);
    }

    public function checkCode(Request $request) {
        $count = UnitOfMeasure::where('uom_code', '=', $request->code)->count();

        if ($request->idcode > 0) {

            $user = UnitOfMeasure::where('uom_id', $request->idcode)->first();

            if ($user->uom_code == $request->code) {
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

    public function saveUom(Request $request) {
        $uom = new UnitOfMeasure();
        if ($uom->validate($request->all())) {
            if ($request->uom_hid > 0) {
                $uom = UnitOfMeasure::find($request->uom_hid);
                $uom->uom_description = $request->uom_description;
                $uom->uom_factor = $request->uom_factor;
                $uom->uom_base_unit = $request->uom_base_unit;
                $uom->unit_type = $request->unit_type;
            } else {
                $uom->fill($request->all());
                $uom->status = 1;
                $uom->created_by = 1;
            }

            $uom = $uom->saveOrFail();
            // echo json_encode(array('Saved'));
            echo json_encode(array('status' => 'success', 'message' => 'UOM details saved successfully.'));
        } else {
            // failure, get errors
            $errors = $uom->errors();
            echo json_encode(array('status' => 'error', 'message' => $errors));
        }
    }

    public function edit(Request $request) {
        $uom_id = $request->uom_id;
        $uom = UnitOfMeasure::find($uom_id);
        echo json_encode($uom);
    }

    public function delete(Request $request) {
        $uom_id = $request->uom_id;
        //$source = Main_Source::find($source_id);
        //$source->delete();
        $uom = UnitOfMeasure::where('uom_id', $uom_id)->update(['status' => 0]);
        echo json_encode(array('delete'));
    }

}
