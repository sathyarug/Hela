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
use Illuminate\Support\Facades\DB;

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
                  $locId=auth()->payload()['loc_id'];
                  $unId = UniqueIdGenerator::generateUniqueId('GRN', auth()->payload()['loc_id']);


                 $header->grn_number = $unId;
                 $header->po_number = $request->header['po_id'];

            }else{
                 $header = GrnHeader::find($request['grn_id']);
                 $header->updated_by = auth()->payload()['user_id'];

                 // Remove all added grn details
                 GrnDetail::where('grn_id', $request['grn_id'])->delete();
            }
            //Get Main store
            $store = SubStore::find($request->header['substore_id']);

            $header->batch_no = $request->header['batch_no'];
            $header->inv_number = $request->header['invoice_no'];
            $header->note = $request->header['note'];
            $header->location = auth()->payload()['loc_id'];
            $header->main_store = $store->store_id;
            $header->sub_store = $store->substore_id;
            $header->sup_id=$request->header['sup_id'];
            $header->created_by = auth()->payload()['user_id'];

            $header->save();

             $i = 1;

             //$valTol = $this->validateSupplierTolerance($request['dataset'], $request->header['sup_id']);

             //for tempary
             $valTol=true;
             foreach ($request['dataset'] as $rec){

                 if($valTol) {

                     $poDetails = PoOrderDetails::find($rec['id']);

                     $grnDetails = new GrnDetail;

                     $grnDetails->grn_id = $header->grn_id;
                     $grnDetails->po_number=$request->header['po_id'];
                     $grnDetails->grn_line_no = $i;
                     $grnDetails->style_id = $poDetails->style;
                     $grnDetails->po_details_id=$rec['id'];
                     $grnDetails->combine_id = $poDetails->comb_id;
                     $grnDetails->color = $poDetails->colour;
                     $grnDetails->size = $poDetails->size;
                     $grnDetails->uom = $poDetails->uom;
                     $grnDetails->po_qty = (double)$poDetails->tot_qty;
                     $grnDetails->grn_qty = (double)$rec['qty'];
                     $grnDetails->bal_qty =(double)$rec['bal_qty'];
                     //$grnDetails->bal_qty = (double)$poDetails->tot_qty - (double)$rec['qty'];
                     $grnDetails->item_code = $poDetails->item_code;

                     $grnDetails->save();

                     $i++;
                     //dd($store->substore_id);
                     //Get Quarantine Bin
                     $bin = StoreBin::where('substore_id', $store->substore_id)
                         ->where('quarantine','=',1)
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
                     $st->customer_po_id=$rec['order_id'];
                     $st->qty = (double)$rec['qty'];
                     $st->location = auth()->payload()['loc_id'];
                     //dd($bin);
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

        $section_list = GrnHeader::select('store_grn_header.grn_number', 'merc_po_order_header.po_number', 'org_supplier.supplier_name', 'store_grn_header.created_date', 'org_store.store_name', 'org_substore.substore_name')
                        ->join('store_grn_detail', 'store_grn_detail.grn_id', '=', 'store_grn_header.grn_id')
                        //->leftjoin('merc_po_order_header','store_grn_header.po_number','=','merc_po_order_header.po_id')
                        ->leftjoin('merc_po_order_header', 'store_grn_detail.grn_id', '=', 'store_grn_header.grn_id')
                        ->leftjoin('org_substore', 'store_grn_header.sub_store', '=', 'org_substore.substore_id')
                        ->leftjoin('org_store', 'org_substore.store_id', '=', 'org_store.store_id')
                        ->leftjoin('org_supplier', 'store_grn_header.sup_id', '=', 'org_supplier.supplier_id')
                        ->orWhere('supplier_name', 'like', $search.'%')
                        ->orWhere('substore_name', 'like', $search.'%')
                        ->orWhere('grn_number', 'like', $search.'%')
                        ->orWhere('merc_po_order_header.po_number', 'like', $search.'%')
                        ->orderBy($order_column, $order_type)
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
    //  dd($dataArr);

        $poQty = 0;
        $qty = 0;
        foreach ($dataArr as $data){
            $qty += $data['qty'];
            $poQty += $data['tot_qty'];

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

    public function fiterData(Request $request){
      //dd($request);
  /*  if($request['customer_name']==null){
    $customer_id=0;
   }
   if($request['customer_po']==null){
     $customer_po=0;
   }
   if($request['color']==null){
     $color=0;
   }
   if($request['item_description']==null){
     $itemDesacription=nul;
   }
   if($request['pcd_date']==null){
     $pcd=null;
   }
   if($request['rm_in_date']==null){
     $rm_in_date=null;
   }*/
    $customer_id=$request['customer_name']['customer_id'];
    $customer_po=$request['customer_po']['order_id'];
    $color=$request['color']['color_id'];
    $itemDesacription=$request['item_description']['master_id'];
    $pcd=$request['pcd_date'];
    $rm_in_date=$request['rm_in_date'];



                              $poData=DB::Select("SELECT DISTINCT style_creation.style_no,
                                           cust_customer.customer_name,merc_po_order_header.po_id,merc_po_order_details.id,
                                           item_master.master_description,
                                           org_color.color_name,
                                          org_size.size_name,
                                          org_uom.uom_code,
                                          merc_po_order_details.tot_qty,
                                          merc_customer_order_details.rm_in_date,
                                          merc_customer_order_details.pcd,
                                          merc_customer_order_details.po_no,
                                           merc_customer_order_header.order_id,
                                           item_master.master_id,
                                           (SELECT
                                          SUM(SGD.grn_qty)
                                          FROM
                                         store_grn_detail AS SGD

                                         WHERE
                                        SGD.po_details_id = merc_po_order_details.id
                                       ) AS tot_grn_qty

                                  FROM
                                 merc_po_order_header
                                INNER JOIN merc_po_order_details ON merc_po_order_header.po_number = merc_po_order_details.po_no
                             LEFT JOIN store_grn_header ON merc_po_order_header.po_id = store_grn_header.po_number
                             INNER JOIN style_creation ON merc_po_order_details.style = style_creation.style_id
                           INNER JOIN cust_customer ON style_creation.customer_id = cust_customer.customer_id
                           INNER JOIN merc_customer_order_header ON style_creation.style_id=merc_customer_order_header.order_style
                           INNER JOIN merc_customer_order_details ON merc_customer_order_header.order_id=merc_customer_order_details.order_id
                          INNER JOIN item_master ON merc_po_order_details.item_code = item_master.master_id
                          INNER JOIN org_color ON merc_po_order_details.colour = org_color.color_id
                           INNER JOIN org_size ON merc_po_order_details.size = org_size.size_id
                         INNER JOIN org_uom ON merc_po_order_details.uom = org_uom.uom_id

                        /* INNER JOIN  store_grn_detail ON store_grn_header.grn_id=store_grn_detail.grn_id*/

                         WHERE merc_customer_order_header.order_id like  '%".$customer_po."'
                        AND cust_customer.customer_id like  '%".$customer_id."'
                        AND org_color.color_id like '%".$color."'
                        AND item_master.master_id like '%".$itemDesacription."'
                        AND merc_customer_order_details.pcd like '%".$pcd."'
                        AND merc_customer_order_details.rm_in_date like '%".$rm_in_date."'
                        GROUP BY merc_po_order_details.id
                        /*AND store_grn_header.grn_id=*/

                ");


                return response([
                    'data' => $poData
                ]);
                  ///return $poData;







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
        //dd($request);
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
