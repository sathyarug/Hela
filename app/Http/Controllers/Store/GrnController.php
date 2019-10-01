<?php

namespace App\Http\Controllers\Store;

use App\Libraries\UniqueIdGenerator;
use App\Models\Store\StoreBin;
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
use App\Libraries\AppAuthorize;
class GrnController extends Controller
{

    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
    }
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
            $y=0;
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
            $header->status=1;
            $header->created_by = auth()->payload()['user_id'];

            $header->save();

             $i = 1;

             //$valTol = $this->validateSupplierTolerance($request['dataset'], $request->header['sup_id']);

             //for tempary
             $valTol=true;
             //dd($request['dataset'] );
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
                     $grnDetails->grn_qty = $rec['qty'];
                     $grnDetails->bal_qty =(double)$rec['bal_qty'];
                     $grnDetails->original_bal_qty=(double)$rec['original_bal_qty'];
                     $grnDetails->maximum_tolarance =$rec['maximum_tolarance'];
                     $grnDetails->item_code = $poDetails->item_code;
                     $grnDetails->excess_qty=(double)$rec['excess_qty'];
                     $grnDetails->status=1;

                     $grnDetails->save();
                     $responseData[$y]=$grnDetails;
                     $y++;
                     $i++;
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
                         'grnId' => $header->grn_id,
                         'detailData'=>$responseData
                     ]
                     ], Response::HTTP_CREATED );
                 }

             }

            return response(['data' => [
                    'type' => 'success',
                    'message' => 'Success! Saved successfully.',
                    'grnId' => $header->grn_id,
                    'detailData'=>$responseData
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

        $section_list = GrnHeader::select('store_grn_header.grn_number', 'store_grn_detail.grn_id','merc_po_order_header.po_number', 'org_supplier.supplier_name', 'store_grn_header.created_date', 'org_store.store_name', 'org_substore.substore_name')
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
      $y=0;
      $header=$request->header;
      $dataset=$request->dataset;
      $grnHeader=GrnHeader::find($id);
        $grnHeader['batch_no']=$header['batch_no'];
        $grnHeader['sub_store']=$header['sub_store']['sub_store'];
        $grnHeader['note']=$header['note'];

        //$store = SubStore::find($request->header['substore_id'])
        $grnHeader->save();
        $bin = StoreBin::where('substore_id', $grnHeader['sub_store'])
            ->where('quarantine','=',1)
            ->first();
        for($i=0;$i<sizeof($dataset);$i++){
          $grnDetails=new GrnDetail;
          if(isset($dataset[$i]['grn_detail_id'])==true){
            $grnDetails=GrnDetail::find($dataset[$i]['grn_detail_id']);
            $grnDetails['grn_qty']=(float)$dataset[$i]['qty'];
            $grnDetails['bal_qty']=(float)$dataset[$i]['bal_qty'];
            $grnDetails->save();


            $poDetails = PoOrderDetails::find($dataset[$i]['id']);

            //Update Stock Transaction
            $transaction = Transaction::where('trans_description', 'GRN')->first();

            $st = new StockTransaction;
            $st->status = 'CONFIRM';
            $st->doc_type = $transaction->trans_code;
            $st->doc_num = $id;
            $st->style_id = $poDetails->style;
            $st->main_store = $grnHeader->main_store;
            $st->sub_store = $grnHeader->sub_store;
            $st->item_code = $poDetails->item_code;
            $st->size = $poDetails->size;
            $st->color = $poDetails->colour;
            $st->uom = $poDetails->uom;
            $st->customer_po_id=$dataset[$i]['order_id'];
            $st->qty = (double)$dataset[$i]['qty'];
            $st->location = auth()->payload()['loc_id'];
            //dd($bin);
            $st->bin = $bin->store_bin_id;
            $st->created_by = auth()->payload()['user_id'];
            $st->save();
            $responseData[$y]=$grnDetails;
            }
            else if(isset($dataset[$i]['grn_detail_id'])==false){
              $poDetails = PoOrderDetails::find($dataset[$i]['id']);
              $max_line_no=DB::table('store_grn_detail')->where('grn_id','=',$id)
                                                        ->max('grn_line_no');
              $grnDetails = new GrnDetail;

              $grnDetails->grn_id =$id;
              $grnDetails->po_number=$header['po_id'];
              $grnDetails->grn_line_no = $max_line_no++;
              $grnDetails->style_id = $poDetails->style;
              $grnDetails->po_details_id=$dataset[$i]['id'];
              $grnDetails->combine_id = $poDetails->comb_id;
              $grnDetails->color = $poDetails->colour;
              $grnDetails->size = $poDetails->size;
              $grnDetails->uom = $poDetails->uom;
              $grnDetails->po_qty = (double)$poDetails->tot_qty;
              $grnDetails->grn_qty = $dataset[$i]['qty'];
              $grnDetails->bal_qty =(double)$dataset[$i]['bal_qty'];
              $grnDetails->maximum_tolarance =$dataset[$i]['maximum_tolarance'];
              $grnDetails->original_bal_qty=(double)$dataset[$i]['original_bal_qty'];
              $grnDetails->item_code = $poDetails->item_code;
              $grnDetails->excess_qty=(double)$dataset[$i]['excess_qty'];
              $grnDetails->status=1;

              $grnDetails->save();

              $poDetails = PoOrderDetails::find($dataset[$i]['id']);

              //Update Stock Transaction
              $transaction = Transaction::where('trans_description', 'GRN')->first();

              $st = new StockTransaction;
              $st->status = 'CONFIRM';
              $st->doc_type = $transaction->trans_code;
              $st->doc_num = $id;
              $st->style_id = $poDetails->style;
              $st->main_store = $grnHeader->main_store;
              $st->sub_store = $grnHeader->sub_store;
              $st->item_code = $poDetails->item_code;
              $st->size = $poDetails->size;
              $st->color = $poDetails->colour;
              $st->uom = $poDetails->uom;
              $st->customer_po_id=$dataset[$i]['order_id'];
              $st->qty = (double)$dataset[$i]['qty'];
              $st->location = auth()->payload()['loc_id'];
              //dd($bin);
              $st->bin = $bin->store_bin_id;
              $st->created_by = auth()->payload()['user_id'];
              $st->save();


              //$line_no++;
              $responseData[$y]=$grnDetails;
            }
            $y++;
        }



        //dd($header['grn_id']);


        return response(['data' => [
                'type' => 'success',
                'message' => 'Success! updated successfully.',
                'grnId' => $header['grn_id'],
                'detailData'=>$responseData
            ]
        ], Response::HTTP_CREATED);




    }

    public function show($id)
    {
      $status=1;
      $headerData=DB::SELECT("SELECT store_grn_header.*, merc_po_order_header.po_number,merc_po_order_header.po_id,org_supplier.supplier_name,org_substore.substore_name
        FROM
        store_grn_header
        INNER JOIN merc_po_order_header ON store_grn_header.po_number=merc_po_order_header.po_id
        INNER JOIN org_supplier ON store_grn_header.sup_id=org_supplier.supplier_id
        INNER JOIN org_substore ON store_grn_header.sub_store=org_substore.substore_id
        WHERE store_grn_header.grn_id=$id"
    );

    $detailsData=DB::SELECT("SELECT DISTINCT  store_grn_detail.*,style_creation.style_no,merc_customer_order_header.order_id,cust_customer.customer_name,org_color.color_name,store_grn_detail.po_qty as tot_qty,store_grn_detail.grn_qty as qty,store_grn_detail.po_number as po_id,merc_po_order_details.id,
      org_size.size_name,org_uom.uom_code,item_master.master_description,item_master.category_id

      from
      store_grn_header
       JOIN store_grn_detail ON store_grn_header.grn_id=store_grn_detail.grn_id
       JOIN style_creation ON store_grn_detail.style_id=style_creation.style_id
       JOIN cust_customer ON style_creation.customer_id=cust_customer.customer_id
       INNER JOIN merc_customer_order_header ON style_creation.style_id = merc_customer_order_header.order_style
       LEFT JOIN org_color ON store_grn_detail.color=org_color.color_id
       LEFT JOIN org_size ON  store_grn_detail.size= org_size.size_id
       LEFT JOIN org_uom ON store_grn_detail.uom=org_uom.uom_id
       JOIN  item_master ON store_grn_detail.item_code= item_master.master_id
       JOIN merc_po_order_header ON store_grn_detail.po_number=merc_po_order_header.po_id
       JOIN  merc_po_order_details ON store_grn_detail.po_details_id=merc_po_order_details.id
      WHERE store_grn_header.grn_id=$id
      AND store_grn_detail.status= $status
      GROUP BY(merc_po_order_details.id)
      ");

    return response([
        'data' =>[
      'headerData'=>  $headerData[0],
      'detailsData'=>$detailsData
      ]
    ]);

    }



    //validate anything based on requirements
    public function validate_data(Request $request){

      $for = $request->for;
      if($for == 'duplicate')
      {
        return response($this->validate_duplicate_code($request->grn_id, $request->invoice_no));
      }
    }


    //check customer code already exists
    private function validate_duplicate_code($id,$code)
    {
      $grnHeader = GrnHeader::where('inv_number','=',$code)->first();
      if($grnHeader == null){
      return ['status' => 'success'];
     }
     else if($grnHeader->grn_id == $id){
     return ['status' => 'success'];
     }
   else {
    return ['status' => 'error','message' => 'Invoice Number already exists'];
   }
    }

    public function fiterData(Request $request){

   $customer_id=$request['customer_name']['customer_id'];
   $customer_po=$request['customer_po']['order_id'];
   $color=$request['color']['color_name'];
   $itemDesacription=$request['item_description']['master_id'];
   $pcd=$request['pcd_date'];
   $rm_in_date=$request['rm_in_date'];
   $po_id=$request['po_id'];
   $supplier_id=$request['supplier_id'];


   //dd($color);
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
                                       item_master.category_id,
                                       (SELECT
                                      SUM(SGD.grn_qty)
                                      FROM
                                     store_grn_detail AS SGD

                                     WHERE
                                    SGD.po_details_id = merc_po_order_details.id
                                    group By(SGD.po_details_id)
                                  ) AS tot_grn_qty,

                                  (SELECT
                                                    bal_qty
                                                       FROM
                                                       store_grn_detail AS SGD2

                                                                   WHERE
                                                                   SGD2.po_details_id = merc_po_order_details.id
                                                                   group By(SGD2.po_details_id)
                                                                 ) AS bal_qty,
                                  (

                                  SELECT
                                  IFNULL(sum(for_uom.max ),0)as maximum_tolarance
                                  FROM
                                  org_supplier_tolarance AS for_uom
                                  WHERE
                                  for_uom.uom_id =  org_uom.uom_id AND
                                  for_uom.category_id = item_master.category_id AND
                                  for_uom.subcategory_id = item_master.subcategory_id
                                ) AS maximum_tolarance


                              FROM
                             merc_po_order_header
                            INNER JOIN merc_po_order_details ON merc_po_order_header.po_number = merc_po_order_details.po_no
                         LEFT JOIN store_grn_header ON merc_po_order_header.po_id = store_grn_header.po_number
                         INNER JOIN style_creation ON merc_po_order_details.style = style_creation.style_id
                       INNER JOIN cust_customer ON style_creation.customer_id = cust_customer.customer_id
                       INNER JOIN merc_customer_order_header ON style_creation.style_id=merc_customer_order_header.order_style
                       INNER JOIN merc_customer_order_details ON merc_customer_order_header.order_id=merc_customer_order_details.order_id
                      INNER JOIN item_master ON merc_po_order_details.item_code = item_master.master_id
                      LEFT JOIN org_supplier_tolarance AS for_category ON item_master.category_id = for_category.category_id
                      LEFT JOIN org_color ON merc_po_order_details.colour = org_color.color_id
                      LEFT JOIN org_size ON merc_po_order_details.size = org_size.size_id
                      LEFT JOIN org_uom ON merc_po_order_details.uom = org_uom.uom_id

                    /* INNER JOIN  store_grn_detail ON store_grn_header.grn_id=store_grn_detail.grn_id*/

                     WHERE merc_po_order_header.po_id = $po_id
                    AND merc_po_order_header.po_sup_code=$supplier_id
                    AND merc_po_order_details.po_status='PLANNED'
                    AND merc_customer_order_header.order_id like  '%".$customer_po."%'
                    AND cust_customer.customer_id like  '%".$customer_id."%'
                    AND item_master.master_id like '%".$itemDesacription."%'
                    AND merc_customer_order_details.pcd like '%".$pcd."%'
                    AND merc_customer_order_details.rm_in_date like '%".$rm_in_date."%'
                    AND merc_po_order_details.tot_qty>(SELECT
                                                          IFNULL(SUM(SGD.grn_qty),0)
                                                          FROM
                                                         store_grn_detail AS SGD

                                                         WHERE
                                                        SGD.po_details_id = merc_po_order_details.id
                                                      )
                    AND (org_color.color_name IS NULL or  org_color.color_name like  '%".$color."%')
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
    public function deleteLine(Request $request){
      //dd($request->line);
      $grnDetails = GrnDetail::find($request->line);
      $grnDetails->status=0;
      $grnDetails->bal_qty=$grnDetails->$grnDetails->po_qty;
      $grnDetails->save();
      return response([
          'data' => [
            'status'=>1,
            'message'=>"Selected GRN line Deleted"
          ]
      ]);
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
    public function isreadyForRollPlan(Request $request){
      $is_type_fabric=DB::table('item_category')->select('category_code')->where('category_id','=',$request->category_id)->first();
      $substorewiseBins=DB::table('org_substore')->select('*')->where('substore_id','=',$request->substore_id)->get();
      $status=0;
      $message="";
      $is_grn_same_qty=DB::table('store_grn_header')
      ->select('*')
      ->join('store_grn_detail','store_grn_header.grn_id','=','store_grn_detail.grn_id')
      ->where('store_grn_header.inv_number','=',$request->invoice_no)
      ->where('store_grn_header.po_number','=',$request->po_id)
      ->where('store_grn_header.grn_id','=',$request->grn_id)
      ->where('store_grn_detail.po_details_id','=',$request->po_line_id)
      ->first();
      //dd($is_grn_same_qty);
      if($is_type_fabric->category_code!='FA'){
        $status=0;
        $is_grn_same_qty=null;
        $message="Selected Item not a Fabric type";
      }
      else if($is_type_fabric->category_code=='FA'){
        //dd($is_type_fabric->category_code);
      if($is_grn_same_qty==null){
            $status=0;
        $message="Error Can't Add Roll Plan";
      }
       else if($is_grn_same_qty!=null){
      if($is_grn_same_qty->grn_qty==$request->qty)
     {
       $is_aLLreaddy_roll_plned=DB::table('store_roll_plan')->select('*')->where('grn_detail_id','=',$is_grn_same_qty->grn_detail_id)->first();
          //dd($is_aLLreaddy_roll_plned);
              if($is_aLLreaddy_roll_plned!=null){
                $status=0;
               $message="Roll Plan Already Added";
                }
       else{
        $status=1;
      }
     }
     else if($is_grn_same_qty->grn_qty!=$request->qty)
        {
           $status=0;
           $message="Error Can't Add Roll Plan";
        }
      }
    }
      return response([
          'data'=> [
            'dataModel'=>$is_grn_same_qty,
             'status'=>$status,
             'message'=>$message,
             'substoreWiseBin'=>$substorewiseBins
            ]
      ]);


    }

}
