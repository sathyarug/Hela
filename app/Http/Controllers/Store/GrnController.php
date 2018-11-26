<?php

namespace App\Http\Controllers\Store;

use App\Libraries\UniqueIdGenerator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Store\GrnHeader;
use App\Models\Store\GrnDetail;
use App\Models\Merchandising\PoOrderHeader;
use App\Models\Merchandising\PoOrderDetails;


class GrnController extends Controller
{
    public function grnDetails() {
        return view('grn.grn_details');
        
    }

    public function index(Request $request){
        echo 'sasasa';
        //dd($request);
        exit;
    }

    public function testPP(){
        echo "ppppp";
    }

    public function store(Request $request){

         //dd($request); exit;

         if($request['id']){

             //$res = GrnDetail::where('grn_id',$request['id'])->delete();

             //Update GRN Header
             $header = GrnHeader::find($request['id']);
             $unId = UniqueIdGenerator::generateUniqueId('GRN', 1);
             //$unId = 2001;
             $header->grn_number = $unId;
             $header->save();

             // Insert New GRN Lines
             $i = 1;
             foreach ($request['grn_lines'] as $rec){
                 //dd($rec['sc_no']);
                 //$poData = new PoOrderDetails;
                 //$poData = PoOrderDetails::where('id', $rec['po_line_id'])->first();
                 $grnLine = GrnDetail::find($rec['grn_line_id']);

                 //Deleting existing GRN Lines
                 GrnDetail::where('id',$rec['grn_line_id'])->delete();

                 $grnDetails = new GrnDetail;
                 $grnDetails->grn_id = $request['id'];
                 $grnDetails->grn_line_no = $i;
                 $grnDetails->style_id = 211;
                 $grnDetails->sc_no = $grnLine->sc_no;
                 $grnDetails->color = $grnLine->color;
                 $grnDetails->size = $grnLine->size;
                 $grnDetails->uom = $grnLine->uom;
                 $grnDetails->po_qty = $rec['po_qty'];
                 $grnDetails->grn_qty = (float)$rec['qty'];
                 $grnDetails->bal_qty = $rec['po_qty'] - (float)$rec['qty'];
                 $grnDetails->status = 0;
                 $grnDetails->item_code = $rec['item_code'];
                 $grnDetails->save();

                 $i++;
             }

             //Insert Stock Transaction

             //Update Stock
         }

        exit;
        /*$lineCount = 0;

        //Check po lines selected
        foreach ($request['item_list'] as $rec){
            if($rec['item_select']){
                $lineCount++;
            }
        }

        if($lineCount > 0){
            if(!$request['id']){
                $grnHeader = new GrnHeader;
                $grnHeader->grn_number = 1002;
                $grnHeader->po_number = $request->po_no;
                $grnHeader->save();
                $grnNo = $grnHeader->grn_id;
            }else{
                $grnNo = $request['id'];
            }

            foreach ($request['item_list'] as $rec){
                if($rec['item_select']){

                    //$poData = new PoOrderDetails;
                    $poData = PoOrderDetails::where('id', $rec['po_line_id'])->first();

                    $grnDetails = new GrnDetail;
                    $grnDetails->grn_id = $grnNo;
                    $grnDetails->grn_line_no = 1;
                    $grnDetails->style_id = 211;
                    $grnDetails->sc_no = $poData->sc_no;
                    $grnDetails->color = $poData->colour;
                    $grnDetails->size = $poData->size;
                    $grnDetails->uom = $poData->uom;
                    $grnDetails->po_qty = $poData->bal_qty;
                    $grnDetails->grn_qty = (float)$rec['qty'];
                    $grnDetails->bal_qty = $poData->bal_qty - (float)$rec['qty'];
                    $grnDetails->status = 0;
                    $grnDetails->item_code = $poData->item_code;
                    $grnDetails->save();

                }
            }

        }

        return response([
            'id' => $grnNo
        ]);*/

    }

    public function addGrnLines(Request $request){
       // dd($request); exit;
        $lineCount = 0;

        //Check po lines selected
        foreach ($request['item_list'] as $rec){
            if($rec['item_select']){
                $lineCount++;
            }
        }

        if($lineCount > 0){
            if(!$request['id']){
                $grnHeader = new GrnHeader;
                $grnHeader->grn_number = 0;
                $grnHeader->po_number = $request->po_no;
                $grnHeader->save();
                $grnNo = $grnHeader->grn_id;
            }else{
                $grnNo = $request['id'];
            }

            $i = 1;
            foreach ($request['item_list'] as $rec){
                if($rec['item_select']){

                    //$poData = new PoOrderDetails;
                    $poData = PoOrderDetails::where('id', $rec['po_line_id'])->first();

                    $grnDetails = new GrnDetail;
                    $grnDetails->grn_id = $grnNo;
                    $grnDetails->grn_line_no = $i;
                    $grnDetails->style_id = 211;
                    $grnDetails->sc_no = $poData->sc_no;
                    $grnDetails->color = $poData->colour;
                    $grnDetails->size = $poData->size;
                    $grnDetails->uom = $poData->uom;
                    $grnDetails->po_qty = $poData->bal_qty;
                    $grnDetails->grn_qty = (float)$rec['qty'];
                    $grnDetails->bal_qty = $poData->bal_qty - (float)$rec['qty'];
                    $grnDetails->status = 0;
                    $grnDetails->item_code = $poData->item_code;
                    $grnDetails->save();

                }
                $i++;
            }

        }

        return response([
            'id' => $grnNo
        ]);
    }

    public function update(Request $request, $id)
    {

        dd($request);
    }

    public function getPoSCList(Request $request){
        dd($request);
        //echo 'xx';
        exit;
    }

    public function loadAddedGrnLInes(Request $request){
        $grnLines = GrnHeader::getGrnLineData($request);

        return response([
            'data' => $grnLines
        ]);
    }

}
