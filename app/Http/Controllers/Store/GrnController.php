<?php

namespace App\Http\Controllers\Store;

use App\Libraries\UniqueIdGenerator;
use App\Models\Store\Stock;
use App\Models\Store\StockTransaction;
use App\Models\Store\SubStore;
use App\Models\Finance\Transaction;
use App\Models\Store\Store;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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

    public function store(Request $request){

       // dd($request);

         if(empty($request['grn_id'])) {

                 //Update GRN Header
                 $header = new GrnHeader;
                 $unId = UniqueIdGenerator::generateUniqueId('GRN', auth()->payload()['loc_id']);
                 $header->grn_number = $unId;
                 $header->po_number = $request['po_no']['po_id'];

            }else{
                 $header = GrnHeader::find($request['grn_id']);
                 $header->updated_by = auth()->payload()['user_id'];

                 // Remove all added grn details
                 GrnDetail::where('grn_id', $request['grn_id'])->delete();
            }
            //Get Main store
            $store = SubStore::find($request['sub_store']['substore_id']);

            $header->batch_no = $request['batch_no'];
            $header->inv_number = $request['invoice_no'];
            $header->note = $request['note'];
            $header->location = auth()->payload()['loc_id'];
            $header->main_store = $store->store_id;
            $header->sub_store = $store->substore_id;
            $header->created_by = auth()->payload()['user_id'];

            $header->save();

             $i = 1;
             foreach ($request['grn_lines'] as $rec){
                 $poDetails = PoOrderDetails::find($rec['po_line_id']);

                 $grnDetails = new GrnDetail;
                 $grnDetails->grn_id = $header->grn_id;
                 $grnDetails->grn_line_no = $i;
                 $grnDetails->style_id = $poDetails->style;
                 $grnDetails->combine_id = $poDetails->comb_id;
                 $grnDetails->color = $poDetails->colour;
                 $grnDetails->size = $poDetails->size;
                 $grnDetails->uom = $poDetails->uom;
                 $grnDetails->po_qty = (double)$poDetails->tot_qty;
                 $grnDetails->grn_qty = (double)$rec['qty'];
                 $grnDetails->bal_qty = (double)$poDetails->tot_qty - (double)$rec['qty'];
                 $grnDetails->item_code = $poDetails->item_code;

                 $grnDetails->save();

                 $i++;

                /* return response([ 'data' => [
                         'message' => 'Saved Successfully',
                         'grnId' => $header->grn_id
                     ]
                    ], Response::HTTP_CREATED );*/

                // continue;
                 //Update Stock Transaction
                 $transaction = Transaction::where('trans_description', 'GRN')->get();

                 //$st = StockTransaction::where('doc_num', $request['id'])->where('doc_type', 'GRN')->get();

                 $st = new StockTransaction;
                 $st->status = 'CONFIRM';
                 $st->doc_type = $header->grn_id;
                 $st->doc_num = $header->grn_id;
                 $st->style_id = $poDetails->style;
                 $st->main_store = $store->store_id;
                 $st->sub_store = $store->substore_id;
                 $st->item_code = $poDetails->item_code;
                 $st->size = $poDetails->size;
                 $st->color = $poDetails->colour;
                 $st->uom = $poDetails->uom;
                 $st->qty = $store->substore_id;
                 $st->location = auth()->payload()['loc_id'];
                 $st->bin = 1;
                 $st->created_by = auth()->payload()['user_id'];
                 $st->save();


                 // Update Stock
                 $stock = Stock::where('item_code', $rec['item_code'])->where('location', 'GRN')->where('store', 'GRN')->where('sub_store', 'GRN')->get();

                 if(!$stock){
                     $stock = new Stock;
                     $stock->item_code = $rec['item_code'];
                     $stock->item_code = $rec['item_code'];
                 }


             }

             //Update Stock


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

                   // dd($poData);

                    $grnDetails = new GrnDetail;
                    $grnDetails->grn_id = $grnNo;
                    $grnDetails->grn_line_no = $i;
                    $grnDetails->style_id = $poData->style;
                    $grnDetails->sc_no = $poData->sc_no;
                    $grnDetails->color = $poData->colour;
                    $grnDetails->size = $poData->size;
                    $grnDetails->uom = $poData->uom;
                    $grnDetails->po_qty = $poData->tot_qty;
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
