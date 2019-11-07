<?php

namespace App\Http\Controllers\Store;
use App\Libraries\UniqueIdGenerator;
use App\Models\Store\MRNHeader;
use App\Models\Store\MRNDetail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Org\Location\Cluster;
//use App\Models\mrn\MRN;
use App\Models\Finance\Transaction;
use App\Models\Store\StockTransaction;
use App\Models\Store\Stock;
class MrnController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $type = $request['type'];
        if($type == 'datatable')   {
          $data = $request->all();
          return response($this->datatable_search($data));
          }
        elseif($type == 'load-mrn'){
            $mrnId = $request['mrn'];
            $locId = $request['loc'];
            return $this->loadMrn($mrnId, $locId);

        }elseif ($type == 'mrn-select'){
            $soId = $request['so'];
            $active = $request->active;
            $fields = $request->fields;

            return $this->loadMrnList($soId, $fields);
        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $header=$request->header;
      $details=$request->dataset;
      $locId=auth()->payload()['loc_id'];
      $unId = UniqueIdGenerator::generateUniqueId('MRN', auth()->payload()['loc_id']);
    //  dd();
      $mrnHeader=new MRNHeader();
      $mrnHeader->mrn_no=$unId;
      $mrnHeader->style_id= $header['style_no']['style_id'];
      $mrnHeader->customer_po_header_id=$header['so_no']['order_id'];
      $mrnHeader->save();


      for($i=0;$i<sizeof($details);$i++){
      $mrndetails=new MRNDetail();
      $mrndetails->mrn_id=$mrnHeader->mrn_id;
      $mrndetails->item_id=$details[$i]['item_id'];
      $mrndetails->color_id=$details[$i]['color'];
      $mrndetails->size_id=$details[$i]['size'];
      $mrndetails->uom=$details[$i]['uom_id'];
      $mrndetails->gross_consumption=$details[$i]['gross_consumption'];
      $mrndetails->wastge=$details[$i]['wastage'];
      $mrndetails->order_qty=$details[$i]['order_qty'];
      $mrndetails->required_qty=$details[$i]['required_qty'];
      $mrndetails->requested_qty=(double)$details[$i]['req_qty'];
      $mrndetails->inv_qty=$details[$i]['inv_qty'];
      $mrndetails->bal_qty=$details[$i]['bal_qty'];
      //find exact line of stock
      $cus_po=$details[$i]['customer_po_id'];
      $style_id=$mrnHeader->style_id;
      $item_code=$details[$i]['item_id'];
      $size=$details[$i]['size'];
    //  $size=1;
      $color=$details[$i]['color'];
      $main_store=$details[$i]['store'];
      $sub_store=$details[$i]['sub_store'];
      $bin=$details[$i]['bin'];
      if($details[$i]['size']==null){
        $size_serach=0;
      }
      else {
        $size_serach=$details[$i]['size'];
      }
      $findStoreStockLine=DB::SELECT ("SELECT * FROM store_stock
                                       WHERE customer_po_id=$cus_po
                                       AND style_id=$style_id
                                       AND item_id=$item_code
                                       or size=$size_serach
                                       AND color=$color
                                       AND location=$locId
                                       AND store=$main_store
                                       AND sub_store=$sub_store
                                       AND bin=$bin
                                       ");
      $stock=Stock::find($findStoreStockLine[0]->id);
      $stock->inv_qty=(double)$stock->inv_qty-(double)$details[$i]['req_qty'];
      $stock->save();
      $transaction = Transaction::where('trans_description', 'MRN')->first();
      //dd($transaction);
      $st = new StockTransaction;
      $st->status = 'PENDING';
      $st->doc_type = $transaction->trans_code;
      $st->doc_num = $mrndetails->mrn_id;
      $st->style_id =   $mrnHeader->style_id;
      $st->main_store = $details[$i]['store'];
      $st->sub_store = $details[$i]['sub_store'];
      $st->item_code = $details[$i]['item_id'];
      $st->size = $details[$i]['size'];
      $st->color = $details[$i]['color'];
      $st->uom = $details[$i]['uom_id'];
      $st->customer_po_id=$details[$i]['customer_po_id'];
      $st->qty =  $details[$i]['req_qty'];
      $st->location = auth()->payload()['loc_id'];
      $st->bin = $details[$i]['bin'];
      $st->created_by = auth()->payload()['user_id'];
      $st->save();

      $mrndetails->save();


    }


            return response(['data' => [
                    'status' => 1,
                    'message' => 'Saved Successfully.',
                    'grnId' => $mrnHeader->mrn_id,
                    'detailData'=>$mrndetails
                ]
            ], Response::HTTP_CREATED);

    }

    //get searched MRN Details for datatable plugin format
    private function datatable_search($data)
    {
      $start = $data['start'];
      $length = $data['length'];
      $draw = $data['draw'];
      $search = $data['search']['value'];
      $order = $data['order'][0];
      $order_column = $data['columns'][$order['column']]['data'];
      $order_type = $order['dir'];

    $mrn_list = MRNHeader::join('store_mrn_detail','store_mrn_header.mrn_id','=','store_mrn_detail.mrn_id')
      ->join('style_creation','store_mrn_header.style_id','=','style_creation.style_id')
      ->join('merc_customer_order_header','store_mrn_header.customer_po_header_id','=','merc_customer_order_header.order_id')
      ->join('usr_login','merc_customer_order_header.created_by','=','usr_login.user_id')
      ->select('store_mrn_header.*','style_creation.style_no','merc_customer_order_header.order_code','usr_login.user_name')
      ->where('style_creation.style_no'  , 'like', $search.'%' )
      ->orWhere('merc_customer_order_header.order_code'  , 'like', $search.'%' )
      ->orderBy($order_column, $order_type)
      ->offset($start)->limit($length)->get();

      $mrn_list_count =  MRNHeader::join('store_mrn_detail','store_mrn_header.mrn_id','=','store_mrn_detail.mrn_id')
        ->join('style_creation','store_mrn_header.style_id','=','style_creation.style_id')
        ->join('merc_customer_order_header','store_mrn_header.customer_po_header_id','=','merc_customer_order_header.order_id')
        ->join('usr_login','merc_customer_order_header.created_by','=','usr_login.user_id')
        ->select('store_mrn_header.*','style_creation.style_no','merc_customer_order_header.order_code','usr_login.user_name')
        ->where('style_creation.style_no'  , 'like', $search.'%' )
        ->orWhere('merc_customer_order_header.order_code'  , 'like', $search.'%' )
      ->count();

      return [
          "draw" => $draw,
          "recordsTotal" => $mrn_list_count,
          "recordsFiltered" => $mrn_list_count,
          "data" => $mrn_list
      ];
    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {



    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function loadMrnList($soId, $fields){

        $mrnList = MRNHeader::getMRNList($soId);

        return response([
            'data' => $mrnList
        ]);

    }

    public function loadMrn($mrnId, $locId){

    }

    public function loadDetails(Request $request ){
      $soNo=$request->so_no;
      $soDetailsID=$request->so_detail_id;
      $custoMerPo=$request->customer_po;
      $styleNo=$request->style_id;

      $data=DB::SELECT("SELECT
merc_customer_order_header.order_id,
store_stock.id,
store_stock.customer_po_id,
store_stock.style_id,
store_stock.item_id,
store_stock.size,
org_size.size_name,
store_stock.color,
store_stock.location,
store_stock.store,
store_stock.sub_store,
store_stock.bin,
store_stock.uom,
store_stock.material_code,
store_stock.weighted_average_price,
store_stock.inv_qty,
store_stock.tolerance_qty,
store_stock.total_qty,
store_stock.transfer_status,
store_stock.status,
store_stock.created_date,
store_stock.created_by,
store_stock.inv_qty,
store_stock.user_loc_id,
item_master.master_description,
item_master.uom_id,
org_uom.uom_code,
bom_details.order_qty,
bom_details.required_qty,
bom_details.wastage,
org_color.color_name,
bom_details.gross_consumption
FROM
merc_customer_order_header
INNER JOIN merc_customer_order_details ON merc_customer_order_header.order_id = merc_customer_order_details.order_id
INNER JOIN bom_header ON merc_customer_order_details.details_id = bom_header.delivery_id
INNER JOIN store_stock ON merc_customer_order_details.details_id = store_stock.customer_po_id
INNER JOIN style_creation ON store_stock.style_id=style_creation.style_id
Inner JOIN bom_details ON bom_header.bom_id = bom_details.bom_id
Inner JOIN item_master ON bom_details.master_id = item_master.master_id
left JOIN org_uom ON item_master.uom_id = org_uom.uom_id
Inner JOIN org_color on bom_details.color_id=org_color.color_id
LEFT JOIN org_size on store_stock.size=org_size.size_id
where merc_customer_order_header.order_id=$soNo
AND merc_customer_order_details.details_id=$soDetailsID
AND style_creation.style_id=$styleNo
GROUP BY store_stock.id");


//dd($deta);
return response(['data' => $data]);
    }
}
