<?php

namespace App\Http\Controllers\Store;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\stores\StoRollDescription;
use App\Models\stores\PoOrderDetails;
use App\Models\store\StoRollFabricinSpection;
use App\Models\Stores\RollPlan;
use App\Models\Store\GrnHeader;
use App\Models\Store\GrnDetail;
use App\Models\Store\FabricInspection;
use App\Models\Finance\Transaction;
use App\Models\Store\StockTransaction;
use App\Models\Store\Stock;
use Exception;
use Illuminate\Support\Facades\DB;
class FabricInspectionController extends Controller
{
  public function index(Request $request)
  {
    $type = $request->type;
    if($type == 'datatable')   {
      $data = $request->all();
      return response($this->datatable_search($data));
    }
    else if($type == 'autoInvoice')    {
      $search = $request->search;
      return response($this->autocomplete_search_invoice($search));
    }

    else if($type == 'autoBatchNO')    {
      $search = $request->search;
      return response($this->autocomplete_search_batchNo($search));
    }
    else if($type=='autoStatusTypes'){
      $search = $request->search;
      return response($this->autocomplete_search_inspection_status($search));
    }
    else {
      $active = $request->active;
      $fields = $request->fields;
      return response([
        'data' => $this->list($active , $fields)
      ]);
    }
  }


  public function store(Request $request)
  {
        //dd(sizeof($request->data));

        $data=$request->data;
      for($i=0;$i<sizeof($data);$i++){
        $fabricInspection = new FabricInspection();
        $fabricInspection->roll_plan_id=$data[$i]['roll_plan_id'];
        $fabricInspection->lot_no=$data[$i]['lot_no'];
        $fabricInspection->invoice_no=$data[$i]['invoice_no'];
        $fabricInspection->batch_no=$data[$i]['batch_no'];
        $fabricInspection->roll_no=$data[$i]['roll_no'];
        $fabricInspection->qty=$data[$i]['qty'];
        $fabricInspection->received_qty=$data[$i]['received_qty'];
        $fabricInspection->bin=$data[$i]['bin'];
        $fabricInspection->width=$data[$i]['width'];
        $fabricInspection->shade=$data[$i]['shade'];
        $fabricInspection->inspection_status=$data[$i]['status_name'];
        $fabricInspection->lab_comment=$data[$i]['lab_comment'];
        $fabricInspection->comment=$data[$i]['comment'];
        $fabricInspection->status=1;
        $fabricInspection->save();
        if($fabricInspection->inspection_status=='PASS'){
          $transaction = Transaction::where('trans_description', 'GRN')->first();
          $rollplanDetail=DB::SELECT("SELECT
                   store_roll_plan.grn_detail_id,
                    store_fabric_inspection.roll_plan_id,
                     store_grn_header.main_store,
                     store_grn_header.sub_store,
                     store_grn_detail.grn_detail_id,
                      store_grn_detail.grn_id,
                      store_grn_detail.grn_line_no,
                     store_grn_detail.style_id,
                    store_grn_detail.combine_id,
                    store_grn_detail.color,
                    store_grn_detail.size,
                    store_grn_detail.uom,
                    store_grn_detail.item_code,
                   store_grn_detail.po_qty,
store_grn_detail.grn_qty,
store_grn_detail.bal_qty,
store_grn_detail.original_bal_qty,
store_grn_detail.po_details_id,
store_grn_detail.po_number,
store_grn_detail.maximum_tolarance,
store_grn_detail.customer_po_id,
store_roll_plan.bin
FROM
store_fabric_inspection
INNER JOIN store_roll_plan ON store_roll_plan.roll_plan_id = store_fabric_inspection.roll_plan_id
INNER JOIN store_grn_detail ON store_grn_detail.grn_detail_id = store_roll_plan.grn_detail_id
INNER JOIN store_grn_header ON store_grn_header.grn_id = store_grn_detail.grn_id
WHERE store_fabric_inspection.roll_plan_id=$fabricInspection->roll_plan_id");
            //update stock transaction table
            //dd($rollplanDetail[0]);
          $st = new StockTransaction;
          $st->status = 'PASS';
          $st->doc_type = $transaction->trans_code;
          $st->doc_num = $rollplanDetail[0]->grn_id;
          $st->style_id = $rollplanDetail[0]->style_id;
          $st->main_store = $rollplanDetail[0]->main_store;
          $st->sub_store = $rollplanDetail[0]->sub_store;
          $st->item_code = $rollplanDetail[0]->item_code;
          $st->size = $rollplanDetail[0]->size;
          $st->color = $rollplanDetail[0]->color;
          $st->uom = $rollplanDetail[0]->uom;
          $st->customer_po_id=$rollplanDetail[0]->customer_po_id;
          $st->qty = $fabricInspection->received_qty;
          $st->location = auth()->payload()['loc_id'];
          $st->bin = $rollplanDetail[0]->bin;
          $st->created_by = auth()->payload()['user_id'];
          $st->save();
          $po_detail_id=$rollplanDetail[0]->po_details_id;
          $loc= auth()->payload()['loc_id'];
          $balanceQty=DB::SELECT("SELECT min(bal_qty)
                        from store_grn_detail
                        where po_details_id=$po_detail_id");
          //find exact line of stock
          $cus_po=$rollplanDetail[0]->customer_po_id;
          $style_id=$rollplanDetail[0]->style_id;
          $item_code=$rollplanDetail[0]->item_code;
          $size=$rollplanDetail[0]->size;
        //  $size=1;
          $color=$rollplanDetail[0]->color;
          $main_store=$rollplanDetail[0]->main_store;
          $sub_store=$rollplanDetail[0]->sub_store;
          $bin=$rollplanDetail[0]->bin;
          if($size==null){
            $size_serach=0;
          }
          else {
            $size_serach=$size;
          }
          $findStoreStockLine=DB::SELECT ("SELECT * FROM store_stock
                                           WHERE customer_po_id=$cus_po
                                           AND style_id=$style_id
                                           AND item_id=$item_code
                                           or size=$size_serach
                                           AND color=$color
                                           AND location=$loc
                                           AND store=$main_store
                                           AND sub_store=$sub_store
                                           AND bin=$bin
                                           ");

        if($findStoreStockLine==null){
          //update the stock table
          $storeUpdate=new Stock();
          $storeUpdate->customer_po_id=$rollplanDetail[0]->customer_po_id;
          $storeUpdate->style_id = $rollplanDetail[0]->style_id;
          $storeUpdate->item_id= $rollplanDetail[0]->item_code;
          $storeUpdate->size = $rollplanDetail[0]->size;
          $storeUpdate->color = $rollplanDetail[0]->color;
          $storeUpdate->location = auth()->payload()['loc_id'];
          $storeUpdate->store = $rollplanDetail[0]->main_store;
          $storeUpdate->sub_store =$rollplanDetail[0]->sub_store;
          $storeUpdate->bin = $rollplanDetail[0]->bin;
          $storeUpdate->uom = $rollplanDetail[0]->uom;
          $storeUpdate->tolerance_qty = $rollplanDetail[0]->maximum_tolarance;
          $storeUpdate->inv_qty = $fabricInspection->received_qty;
          $storeUpdate->total_qty = $fabricInspection->received_qty;
          $storeUpdate->transfer_status="GRN";
          $storeUpdate->status=1;
          $storeUpdate->save();
        }
          else if($findStoreStockLine!=null){
            //dd($findStoreStockLine[0]->id);
            $stock=Stock::find($findStoreStockLine[0]->id);
            $stock->total_qty=$stock->total_qty+$fabricInspection->received_qty;
           $stock->inv_qty = $stock->inv_qty+$fabricInspection->received_qty;
           $stock->save();
          }


      }


      }
        return response([ 'data' => [
          'message' => 'Fabric Inspection Saved Saved',
          'status' => 1
          ]
        ], Response::HTTP_CREATED );


  }

  private function autocomplete_search_invoice($search){

    $invoice_list = GrnHeader::select('inv_number')->distinct('inv_number')
    ->where([['inv_number', 'like', '%' . $search . '%'],]) ->get();
    return $invoice_list;



  }
  private function autocomplete_search_batchNo($search){
    $invoice_list = GrnHeader::select('batch_no')->distinct('batch_no')
    ->where([['batch_no', 'like', '%' . $search . '%'],]) ->get();
    return $invoice_list;

  }
  private function autocomplete_search_inspection_status($search){
    //dd($search);
    $inspectionStatus=DB::table('store_inspec_status')->where('status_name','like','%'.$search.'%')->pluck('status_name');
    return $inspectionStatus;

  }
/*    public function store(Request $request)
    {


        foreach ($request->all()['roll_info'] as $key => $value)
        {
            $fabricinSpection= new StoRollFabricinSpection();

//            print_r($value);exit;
//            $fabricinSpection->item_code=$value['roll_no'];
            $fabricinSpection->roll_no=$value['roll_no'];
            $fabricinSpection->purchase_weight=$value['purchase_weight'];
            $fabricinSpection->save();

        }exit;

    }
*/
    public function search_rollPlan_details(Request $request){
      $batch_no=$request->batchNo;
      $invoice_no=$request->invoiceNo;
      //dd($batch_no);
      $rollPlanDetails=DB::SELECT("SELECT store_roll_plan.*,org_store_bin.store_bin_name
          From store_roll_plan
          INNER JOIN store_grn_detail on store_roll_plan.grn_detail_id=store_grn_detail.grn_detail_id
          INNER JOIN store_grn_header on store_grn_detail.grn_id=store_grn_header.grn_id
          INNER JOIN org_store_bin on store_roll_plan.bin=org_store_bin.store_bin_id
          WHERE store_roll_plan.invoice_no='".$invoice_no."'
          AND store_roll_plan.batch_no='".$batch_no."'");

          return response([
              'data' => $rollPlanDetails
          ]);
    }

}
