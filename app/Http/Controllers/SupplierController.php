<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\OrgSupplier;
use App\OrgLocation;

class SupplierController extends Controller
{
    public function view()
    {
       

        return view('supplier/supplier', ['loc' =>OrgLocation::all()->toArray()]);
    }

    public function getList() {
        return datatables()->of(OrgSupplier::all())->toJson();
    }

    public function saveSupplier(Request $request) {

        dump($request->all());exit;

//        $main_source = new Main_Source();
//        if ($main_source->validate($request->all()))
//        {
//            if($request->source_hid > 0){
//                $main_source = Main_Source::find($request->source_hid);
//            }
//            $main_source->fill($request->all());
//            $main_source->status = 1;
//            $main_source->created_by = 1;
//            $result = $main_source->saveOrFail();
//            // echo json_encode(array('Saved'));
//            echo json_encode(array('status' => 'success' , 'message' => 'Source details saved successfully.') );
//        }
//        else
//        {
//            // failure, get errors
//            $errors = $main_source->errors();
//            echo json_encode(array('status' => 'error' , 'message' => $errors));
//        }


    }
}
