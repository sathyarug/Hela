<?php

namespace App\Http\Controllers\Store;

use App\Libraries\UniqueIdGenerator;
use App\Models\Store\Stock;
use App\Models\Store\StockTransaction;
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

                 //Update Stock Transaction
                 $st = StockTransaction::where('doc_num', $request['id'])->where('doc_type', 'GRN')->get();

                 foreach($st as $stockTr){
                     $stockTr->status = 'CONFIRM';
                     $stockTr->main_store = 2;
                     $stockTr->sub_store = 1;
                     $stockTr->save();
                 }

                 // Update Stock
                 $stock = Stock::where('item_code', $rec['item_code'])->where('location', 'GRN')->where('store', 'GRN')->where('sub_store', 'GRN')->get();

                 if(!$stock){
                     $stock = new Stock;
                     $stock->item_code = $rec['item_code'];
                     $stock->item_code = $rec['item_code'];
                 }




             }





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

    public function saveGrnBins(Request $request){

        $grnData = GrnDetail::find($request->line_id);
        foreach ($request->bin_list as $bin){
            $stockTrrans = new StockTransaction;
            $stockTrrans->bin = $bin['bin'];
            $stockTrrans->qty = $bin['qty'];
            $stockTrrans->so = $grnData->sc_no;
            $stockTrrans->doc_type = 'GRN';
            $stockTrrans->doc_num = $request->id;
            $stockTrrans->item_code = $grnData->item_code;
            $stockTrrans->size = $grnData->size;
            $stockTrrans->color = $grnData->color;
            $stockTrrans->uom = $grnData->uom;
            $stockTrrans->location = 10;
            $stockTrrans->created_by = 1000;
            $stockTrrans->status = 'PENDING';
            $stockTrrans->save();
        }

    }

    public function update(Request $request, $id)
    {

        dd($request);
    }

    public function destroy($id)
    {
       GrnDetail::where('id',$id)->delete();

    }

    public function getPoSCList(Request $request){
        dd($request);
        //echo 'xx';
        exit;
    }

    public function getAddedBins(Request $request){
      //    dd($request);
       $grnData = GrnDetail::getGrnLineDataWithBins($request->id);

        return response([
            'data' => $grnData
        ]);
        //$grnData = GrnDetail::where('id', $request->lineId)->first();
        //dd($grnData);
    }

    public function loadAddedGrnLInes(Request $request){
        $grnLines = GrnHeader::getGrnLineData($request);

        return response([
            'data' => $grnLines
        ]);
    }

}
