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
        if($type == 'get-invoice-and-supplier'){
            return response([
                'data' => $this->getSupplierAndInvoiceNo($active , $fields, $request->id)
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

    public function loadPoBpoList(Request $request){
        $po = PoOrderHeader::select('po_id', 'po_number')->where('po_id', $request->id)->get();
        $podata = $po->toArray();

        return response([
            'data' => $podata
        ]);
    }

    public function getPoSCList(Request $request){
        $po = PoOrderDetails::select('sc_no')->where('po_id', $request->id)->get();
        $podata = $po->toArray();

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

    //generate pdf
        public function generate_pdf(Request $request)
        {
           // $result = PoOrderHeader::select('po_number','po_date','po_status','po_sup_code','po_deli_loc')->where('po_number', $request->po_no)->get();

                    $result = DB::table('merc_po_order_header')
                            ->join('org_supplier', 'merc_po_order_header.po_sup_code', '=', 'org_supplier.supplier_id')
                            ->join('org_location', 'merc_po_order_header.po_deli_loc', '=', 'org_location.loc_id')
                            ->select('merc_po_order_header.*', 'org_supplier.supplier_name','org_supplier.supplier_address1','org_supplier.supplier_address2',
                            'org_supplier.supplier_city','org_supplier.supplier_country','org_location.loc_name','org_location.loc_address_1','org_location.loc_address_2')
                            ->where('merc_po_order_header.po_number', $request->po_no)->get();

            $total_qty=PoOrderDetails::select('tot_qty')->where('po_no',$request->po_no)->sum('tot_qty');
           // $list=PoOrderDetails::select('*')->where('po_no', $request->po_no)->get();
            $list=DB::table('merc_po_order_details')
                    ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
                    ->join('style_creation', 'merc_po_order_details.style', '=', 'style_creation.style_id')
                    ->join('org_color', 'merc_po_order_details.colour', '=', 'org_color.color_id')
                    ->join('org_size', 'merc_po_order_details.size', '=', 'org_size.size_id')
                    ->join('org_uom', 'merc_po_order_details.uom', '=', 'org_uom.uom_id')
                    ->select('merc_po_order_details.*', 'item_master.master_code', 'item_master.master_description',
                    'style_creation.style_no','org_color.color_name','org_size.size_name','org_uom.uom_code')
                    ->where('merc_po_order_details.po_no', $request->po_no)->get();

                if($result){
                    $data=[
                        'po'=>$result[0]->po_number,
                        'po_date'=>$result[0]->po_date,
                        'po_status'=>$result[0]->po_status,
                        'supplier_name'=>$result[0]->supplier_name,
                        'supplier_address1'=>$result[0]->supplier_address1,
                        'supplier_address2'=>$result[0]->supplier_address2,
                        'supplier_city'=>$result[0]->supplier_city,
                        'supplier_country'=>$result[0]->supplier_country,
                        'loc_name'=>$result[0]->loc_name,
                        'loc_address_1'=>$result[0]->loc_address_1,
                        'loc_address_2'=>$result[0]->loc_address_2,
                        'total_qty'=>$total_qty,
                        'data'=>$list,
                    ];

                }

            $pdf=PDF::loadView('pdf', $data);
            return $pdf->stream('document.pdf');
        }




}
