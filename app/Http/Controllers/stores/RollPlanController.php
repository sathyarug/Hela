<?php

namespace App\Http\Controllers\stores;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\stores\PoOrderDetails;
use App\Models\stores\PoOrderHeader;
use App\Models\stores\PoOrderType;
use DB;

class RollPlanController extends Controller
{
    public function index(Request $request)
    {
//        $PoOrderDetails = new PoOrderDetails();
//        $all=$PoOrderDetails::find(1)->PoOrderHeader();
//
//        dd($all->po_id);exit;
        $draw = $request['draw'];
        $po = $request['text'];

        $data=DB::table('po_order_details')
            ->join('po_order_header', 'po_order_details.po_id', '=', 'po_order_header.po_id')
            ->join('po_order_type', 'po_order_header.po_status', '=', 'po_order_type.po_type_id')
            ->select('*')
            ->where('po_order_type.po_status_name','<>','CANCEL')
            ->where('po_order_header.po_id','=',$po)
            ->get();
//           ->toJson();
//            ->make(true);

//        foreach ($data AS $info){
//
//            $dataTable[]=$info;
//        }
//       print_r($data);exit;
        return json_encode([
            "draw" => $draw,
            "recordsTotal" => 1,
            "recordsFiltered" =>1,
            "data" =>  $data
        ]);


    }
}
