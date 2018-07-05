<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\OrgSupplier;
use App\OrgLocation;
use App\Currency;
use App\OrgShipmentMode;
use App\Models\Org\OriginType;
use App\Models\Finance\Accounting\PaymentMethod;
use App\Models\Finance\Accounting\PaymentTerm;

class SupplierController extends Controller
{
    public function view()
    {
        $locs=OrgLocation::all()->toArray();
        $PaymentMethods=PaymentMethod::all()->toArray();
        $PaymentTerms=PaymentTerm::all()->toArray();
        $CurrencyListAll=Currency::all()->toArray();

        $loction=array(''=>'');
        foreach ($locs AS $loc ){
            $loction[$loc['loc_id']]=$loc['loc_name'];
        }
        $method=array(''=>'');
        foreach ($PaymentMethods AS $PaymentMethod ){
            $method[$PaymentMethod['payment_method_id']]=$PaymentMethod['payment_method_code'];
        }
        $terms=array(''=>'');
        foreach ($PaymentTerms AS $PaymentTerm ){
            $terms[$PaymentTerm['payment_term_id']]=$PaymentTerm['payment_code'];
        }
        $currency=array(''=>'');
        foreach ($CurrencyListAll AS $CurrencyList ){
            $currency[$CurrencyList['currency_id']]=$CurrencyList['currency_code'];
        }


        return view('supplier/supplier', ['loc' =>$loction,'method'=>$method,'terms'=>$terms,'currency'=>$currency]);
    }

    public function getList() {
        return datatables()->of(OrgSupplier::all()->sortByDesc("supplier_id")->sortByDesc("status"))->toJson();
    }

    public function saveSupplier(Request $request) {

        $OrgSupplier = new OrgSupplier();
        if ($OrgSupplier->validate($request->all()))
        {
            if($request->supplier_hid > 0){
                $OrgSupplier = OrgSupplier::find($request->supplier_hid);
            }
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

    public function loadEditSupplier(Request $request) {
        $Supplier_id = $request->id;
        $Supplier = OrgSupplier::find($Supplier_id);
        echo json_encode($Supplier);
    }

    public function deleteSupplier(Request $request) {
        $Supplier_id = $request->id;
        $source = OrgSupplier::where('supplier_id', $Supplier_id)->update(['status' => 0]);
        echo json_encode(array('delete'));
    }

    public function loadAddEditSupplier(Request $request) {

        $Supplier_id = $request->id;
        $Supplier = OrgSupplier::find($Supplier_id);

//        print_r($Supplier->supplier_name);exit;

        $locs=OrgLocation::all()->toArray();
        $PaymentMethods=PaymentMethod::all()->toArray();
        $PaymentTerms=PaymentTerm::all()->toArray();
        $CurrencyListAll=Currency::all()->toArray();
        $originListAll=OriginType::all()->toArray();
        $OrgShipmentModeListAll=OrgShipmentMode::all()->toArray();

        $loction=array(''=>'');
        foreach ($locs AS $loc ){
            $loction[$loc['loc_id']]=$loc['loc_name'];
        }
        $method=array(''=>'');
        foreach ($PaymentMethods AS $PaymentMethod ){
            $method[$PaymentMethod['payment_method_id']]=$PaymentMethod['payment_method_code'];
        }
        $terms=array(''=>'');
        foreach ($PaymentTerms AS $PaymentTerm ){
            $terms[$PaymentTerm['payment_term_id']]=$PaymentTerm['payment_code'];
        }
        $currency=array(''=>'');
        foreach ($CurrencyListAll AS $CurrencyList ){
            $currency[$CurrencyList['currency_id']]=$CurrencyList['currency_code'];
        }
        $origin=array(''=>'');
        foreach ($originListAll AS $originList ){
            $origin[$originList['origin_type_id']]=$originList['origin_type'];
        }
        return view('supplier.frmsupplier',['loc' =>$loction,'method'=>$method,'terms'=>$terms,'currency'=>$currency,'Supplier'=>$Supplier,'origin'=>$origin]);
    }
}
