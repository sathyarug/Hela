<?php

namespace App\Http\Controllers\Store;

use App\Models\Store\MRNHeader;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Org\Location\Cluster;
use App\Models\mrn\MRN;

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
        if($type == 'datatable') {
            $draw = $request['draw'];
            $po = $request['text'];
            return $this->dataTable($draw, $po);
        }elseif($type == 'load-mrn'){
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
where merc_customer_order_header.order_id=$soNo
AND merc_customer_order_details.details_id=$soDetailsID
AND style_creation.style_id=$styleNo
GROUP BY store_stock.id");


//dd($deta);
return response(['data' => $data]);
    }
}
