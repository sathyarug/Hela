<?php

namespace App\Http\Controllers\Merchandising;

use App\Models\Merchandising\PoOrderDetails;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Merchandising\PoOrderHeader;

use Illuminate\Support\Facades\DB;

class PurchaseOrder extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $type = $request->type;
        $active = $request->active;
        $fields = $request->fields;
        if($type == 'get-invoice-and-supplier') {
            return response([
                'data' => $this->getSupplierAndInvoiceNo($active, $fields, $request->id)
            ]);
        }elseif($type == 'color-list'){
            return response([
                'data' => $this->getPoColorList($request->id)
            ]);
        }else{
            return response([
                'data' => $this->list($active , $fields)
            ]);
        }


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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

    public function loadPoLineData(Request $request){
        $podata = PoOrderHeader::getPoLineData($request);
        return response([
            'data' => $podata
        ]);
        //return response()->json(true);
    }

    public function getPoSCList(Request $request){
         $poData = DB::table('merc_po_order_header as h')
            ->join('merc_po_order_details as d', 'h.po_number', '=', 'd.po_no')
            ->select('d.sc_no')
            ->where('h.po_id', '=', $request->id)
            ->toSql();

         dd($poData);


        $podata = $poData->toArray();

        return response([
            'data' => $podata
        ]);
    }

    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
        $query = null;
        if($fields == null || $fields == '') {
            $query = PoOrderHeader::select('*');
        }
        else{
            $fields = explode(',', $fields);
            $query = PoOrderHeader::select($fields);
            if($active != null && $active != ''){
                $query->where([['status', '=', $active]]);
            }
        }
        return $query->get();
    }

    public function getSupplierAndInvoiceNo($active = 0 , $fields = null, $id){
        $poHeader = PoOrderHeader::find($id);
        return $poHeader->getPOSupplierAndInvoice();
    }

    public function getPoColorList($id){
        $poData = DB::table('merc_po_order_header as h')
            ->join('merc_po_order_details as d', 'h.po_number', '=', 'd.po_no')
            ->join('org_color as c', 'c.color_id', '=', 'd.colour')
            ->select('c.color_id', 'c.color_name')
            ->where('h.po_id', '=', $id)
            ->groupBy('d.colour')
            ->get();

        return $poData->toArray();

    }
}
