<?php

namespace App\Http\Controllers\Store;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\stores\StoRollDescription;
use App\Models\stores\PoOrderDetails;
use DB;

class FabricInspectionController extends Controller
{
    public function index(Request $request)
    {
        $dataForRoll=DB::table('po_order_details')
            ->join('po_order_header', 'po_order_details.po_id', '=', 'po_order_header.po_id')
            ->join('po_order_type', 'po_order_header.po_status', '=', 'po_order_type.po_type_id')
            ->join('sto_roll_description', 'sto_roll_description.item_code', '=', 'po_order_details.item_code')
            ->select('*')
            ->where('po_order_type.po_status_name','<>','CANCEL')
            ->where('sto_roll_description.status','=',1);


        //dd($dataForRoll->where('po_order_type.po_status_name','<>','CANCEL')->get());
            $data=$request->all();


        if ($data['po_no'] != '') {
            $dataForRoll->where('po_order_details.po_id','=',$data['po_no']);

        }

        if ($data['sc_no'] != '') {
            $dataForRoll->where('po_order_details.sc_no','=',$data['sc_no']);

        }

        if ($data['batch_no'] != '') {
            $dataForRoll->where('sto_roll_description.roll_no','=',$data['batch_no']);

        }

        if ($data['description'] != '') {
            $dataForRoll->where('sto_roll_description.comment','=',$data['description']);

        }

//        dd($data['sc_no']);

        $results = $dataForRoll->get();
        return $results;
//        dd($results);




    }
}
