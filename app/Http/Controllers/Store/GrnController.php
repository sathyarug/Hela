<?php

namespace App\Http\Controllers\Store;

use App\Libraries\UniqueIdGenerator;
use App\Models\Org\Store\StoreBin;
use App\Models\Org\SupplierTolarance;
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
        $type = $request->type;
       // $fields = $request->fields;
       // $active = $request->status;
        if($type == 'datatable') {
            $data = $request->all();
            return response($this->datatable_search($data));
        }
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

             $valTol = $this->validateSupplierTolerance($request['grn_lines'], $request->sup_id);

             foreach ($request['grn_lines'] as $rec){

                 if($valTol) {

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

                     //Get Quarantine Bin
                     $bin = StoreBin::where('substore_id', $store->substore_id)
                         ->where('quarantine', 1)
                         ->first();


                     //Update Stock Transaction
                     $transaction = Transaction::where('trans_description', 'GRN')->first();

                     $st = new StockTransaction;
                     $st->status = 'CONFIRM';
                     $st->doc_type = $transaction->trans_code;
                     $st->doc_num = $header->grn_id;
                     $st->style_id = $poDetails->style;
                     $st->main_store = $store->store_id;
                     $st->sub_store = $store->substore_id;
                     $st->item_code = $poDetails->item_code;
                     $st->size = $poDetails->size;
                     $st->color = $poDetails->colour;
                     $st->uom = $poDetails->uom;
                     $st->qty = (double)$rec['qty'];
                     $st->location = auth()->payload()['loc_id'];
                     $st->bin = $bin->store_bin_id;
                     $st->created_by = auth()->payload()['user_id'];
                     if (!$st->save()) {
                         return response(['data' => [
                             'type' => 'error',
                             'message' => 'Not Saved',
                             'grnId' => $header->grn_id
                         ]
                         ], Response::HTTP_CREATED);
                     }
                 }else{
                     return response([ 'data' => [
                         'type' => 'error',
                         'message' => 'Not matching with supplier tolerance.',
                         'grnId' => $header->grn_id
                     ]
                     ], Response::HTTP_CREATED );
                 }

             }

            return response(['data' => [
                    'type' => 'success',
                    'message' => 'Success! Saved successfully.',
                    'grnId' => $header->grn_id
                ]
            ], Response::HTTP_CREATED);


    }

    public function datatable_search($data){
        $start = $data['start'];
        $length = $data['length'];
        $draw = $data['draw'];
        $search = $data['search']['value'];
        $order = $data['order'][0];
        $order_column = $data['columns'][$order['column']]['data'];
        $order_type = $order['dir'];

        $section_list = GrnHeader::select('store_grn_header.grn_id', 'store_grn_header.grn_number', 'store_grn_header.po_number', 'org_supplier.supplier_name', 'store_grn_header.created_date', 'org_store.store_name', 'org_substore.substore_name')
                        ->join('store_grn_detail', 'store_grn_detail.grn_id', '=', 'store_grn_header.grn_id')
                        ->leftjoin('merc_po_order_header', 'store_grn_detail.grn_id', '=', 'store_grn_header.grn_id')
                        ->leftjoin('org_substore', 'store_grn_header.sub_store', '=', 'org_substore.substore_id')
                        ->leftjoin('org_store', 'org_substore.store_id', '=', 'org_store.store_id')
                        ->leftjoin('org_supplier', 'store_grn_header.sup_id', '=', 'org_supplier.supplier_id')
                        ->orderBy('store_grn_header.created_date',$order_column.' DESC', $order_type)
                        ->groupBy('store_grn_header.grn_id')
                        ->offset($start)->limit($length)->get();
                        //->where('stock_grn_header'  , '=', $search.'%' )


        $section_count = GrnHeader::where('grn_number'  , 'like', $search.'%' )
            //->orWhere('style_description'  , 'like', $search.'%' )
            ->count();

        return [
            "draw" => $draw,
            "recordsTotal" => $section_count,
            "recordsFiltered" => $section_count,
            "data" => $section_list
        ];
    }

    public function validateSupplierTolerance($dataArr, $suppId){

        $poQty = 0;
        $qty = 0;
        foreach ($dataArr as $data){
            $qty += $data['qty'];
            $poQty += $data['po_qty'];

        }

        //Get Supplier Tolarance
        $supTol = SupplierTolarance::where('supplier_id', $suppId)->first();

        $tolQty = $poQty*($supTol->tolerance_percentage/100);
        $plusQty = $tolQty + $poQty;
        $minusQty = $poQty - $tolQty;
        if($qty >= $minusQty || $qty <= $plusQty){
            return true;
        }else{
            return false;
        }


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
        dd($request);
        $grnData = GrnDetail::find($request->line_id);
       /* foreach ($request->bin_list as $bin){
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
        }*/

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
