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
        $locs=OrgLocation::all()->toArray();
//        dd($locs);exit;
        $loction=array(''=>'');
        foreach ($locs AS $loc ){
            $loction[$loc['loc_id']]=$loc['loc_name'];
        }

        return view('supplier/supplier', ['loc' =>$loction]);
    }

    public function getList() {
        return datatables()->of(OrgSupplier::all())->toJson();
    }

    public function saveSupplier(Request $request) {


        $OrgSupplier = new OrgSupplier();
        if ($OrgSupplier->validate($request->all()))
        {
//            if($request->source_hid > 0){
//                $main_source = OrgSupplier::find($request->source_hid);
//            }
            $OrgSupplier->fill($request->all());
            $OrgSupplier->status = 1;
            $OrgSupplier->created_by = 1;
            $result = $OrgSupplier->saveOrFail();
            echo json_encode(array('status' => 'success' , 'message' => 'Source details saved successfully.') );
        }
        else
        {
            // failure, get errors
            $errors = $OrgSupplier->errors();
            echo json_encode(array('status' => 'error' , 'message' => $errors));
        }


    }
}
