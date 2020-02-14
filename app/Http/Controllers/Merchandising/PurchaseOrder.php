<?php

namespace App\Http\Controllers\Merchandising;

use App\Models\Merchandising\PoOrderDetails;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Merchandising\PoOrderHeader;

use Illuminate\Support\Facades\DB;
use PDF;

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
        if ($type == 'get-invoice-and-supplier') {
            return response([
                'data' => $this->getSupplierAndInvoiceNo($active, $fields, $request->id)
            ]);
        } elseif ($type == 'color-list') {
            return response([
                'data' => $this->getPoColorList($request->id)
            ]);
        } else {
            return response([
                'data' => $this->list($active, $fields)
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

    public function loadPoLineData(Request $request)
    {
        $podata = PoOrderHeader::getPoLineData($request);
        return response([
            'data' => $podata
        ]);
        //return response()->json(true);
    }

    public function getPoSCList(Request $request)
    {
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
    private function list($active = 0, $fields = null)
    {
        $query = null;
        if ($fields == null || $fields == '') {
            $query = PoOrderHeader::select('*');
        } else {
            $fields = explode(',', $fields);
            $query = PoOrderHeader::select($fields);
            if ($active != null && $active != '') {
                $query->where([['status', '=', $active]]);
            }
        }
        return $query->get();
    }

    public function getSupplierAndInvoiceNo($active = 0, $fields = null, $id)
    {
        $poHeader = PoOrderHeader::find($id);
        return $poHeader->getPOSupplierAndInvoice($poHeader->po_sup_code);
    }

    //generate pdf
    public function generate_pdf(Request $request)
    {

        // $result = PoOrderHeader::select('po_number','po_date','po_status','po_sup_code','po_deli_loc')->where('po_number', $request->po_no)->get();

        $result = DB::table('merc_po_order_header')
            ->join('org_supplier', 'merc_po_order_header.po_sup_code', '=', 'org_supplier.supplier_id')
            ->join('org_location', 'merc_po_order_header.po_deli_loc', '=', 'org_location.loc_id')
            ->join('org_company', 'org_company.company_id', '=', 'merc_po_order_header.invoice_to')
            ->join('usr_profile', 'merc_po_order_header.created_by', '=', 'usr_profile.user_id')
            ->join('org_country', 'org_supplier.supplier_country', '=', 'org_country.country_id')
            ->join('fin_currency', 'merc_po_order_header.po_def_cur', '=', 'fin_currency.currency_id')
            ->join('fin_payment_method', 'merc_po_order_header.pay_mode', '=', 'fin_payment_method.payment_method_id')
            ->join('fin_payment_term', 'merc_po_order_header.pay_term', '=', 'fin_payment_term.payment_term_id')
            ->join('fin_shipment_term', 'merc_po_order_header.ship_term', '=', 'fin_shipment_term.ship_term_id')
            ->join('merc_po_order_details', 'merc_po_order_details.po_header_id', '=', 'merc_po_order_header.po_id')
            ->join('merc_shop_order_detail', 'merc_shop_order_detail.shop_order_detail_id', '=', 'merc_po_order_details.shop_order_detail_id')
            ->join('merc_shop_order_header', 'merc_shop_order_header.shop_order_id', '=', 'merc_po_order_details.shop_order_id')
            ->leftJoin('merc_customer_order_details', 'merc_customer_order_details.shop_order_id', '=', 'merc_shop_order_header.shop_order_id')
            ->join('merc_customer_order_header', 'merc_customer_order_header.order_id', '=', 'merc_customer_order_details.order_id')
            ->join('cust_division', 'cust_division.division_id', '=', 'merc_customer_order_header.order_division')
            ->select(
                'merc_po_order_header.*',
                'org_supplier.supplier_code',
                'org_supplier.supplier_name',
                'org_supplier.supplier_address1',
                'org_supplier.supplier_address2',
                'org_supplier.supplier_city',
                'org_supplier.supplier_country',
                'org_country.country_description',
                'org_location.loc_name',
                'org_location.loc_address_1',
                'org_location.loc_address_2',
                'org_company.company_name',
                'org_company.company_address_1',
                'usr_profile.first_name',
                'cust_division.division_description',
                'fin_payment_method.payment_method_description',
                'fin_payment_term.payment_description',
                'fin_shipment_term.ship_term_description',
                'fin_currency.currency_code'
            )
            ->where('merc_po_order_header.po_number', $request->po_no)
            ->distinct()
            ->get();

        $total_qty = PoOrderDetails::select('tot_qty')->where('po_no', $request->po_no)->sum('tot_qty');
        $words = $this->displaywords($total_qty);

        // $list=PoOrderDetails::select('*')->where('po_no', $request->po_no)->get();
        $list = DB::table('merc_po_order_details')
            ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
            ->join('style_creation', 'merc_po_order_details.style', '=', 'style_creation.style_id')
            ->leftjoin('org_color', 'merc_po_order_details.colour', '=', 'org_color.color_id')
            ->leftjoin('org_size', 'merc_po_order_details.size', '=', 'org_size.size_id')
            ->join('org_uom', 'merc_po_order_details.uom', '=', 'org_uom.uom_id')
            ->select(
                'merc_po_order_details.*',
                'item_master.master_code',
                'item_master.master_description',
                'style_creation.style_no',
                'org_color.color_name',
                'org_size.size_name',
                'org_uom.uom_code'
            )
            ->where('merc_po_order_details.po_no', $request->po_no)->get();

        $splitList = DB::table('merc_po_order_details')
            ->join('merc_po_order_split', 'merc_po_order_split.po_details_id', '=', 'merc_po_order_details.id')
            ->select(
                'merc_po_order_split.po_details_id',
                'merc_po_order_split.split_qty',
                'merc_po_order_split.delivery_date'
            )
            ->where('merc_po_order_details.po_no', $request->po_no)
            ->get();

        $count = DB::table('merc_po_order_details')
            ->join('merc_po_order_split', 'merc_po_order_split.po_details_id', '=', 'merc_po_order_details.id')
            ->select(
                'merc_po_order_split.po_details_id',
                DB::raw("COUNT(merc_po_order_split.po_details_id) AS cou")
            )
            ->where('merc_po_order_details.po_no', $request->po_no)
            ->groupBy('merc_po_order_split.po_details_id')
            ->get();

        $groupList = DB::table('merc_po_order_details')
            ->join('item_master', 'merc_po_order_details.item_code', '=', 'item_master.master_id')
            ->leftjoin('org_color', 'merc_po_order_details.colour', '=', 'org_color.color_id')
            ->join('org_uom', 'merc_po_order_details.uom', '=', 'org_uom.uom_id')
            ->select(
                'merc_po_order_details.line_no',
                'merc_po_order_details.deli_date',
                'merc_po_order_details.unit_price',
                'item_master.master_code',
                'item_master.master_description',
                'org_color.color_name',
                'org_uom.uom_code'
            )
            ->where('merc_po_order_details.po_no', $request->po_no)
            ->groupBy('item_master.master_id', 'merc_po_order_details.deli_date', 'org_color.color_id')
            ->get();

        if ($result) {
            $data = [
                'po' => $result[0]->po_number,
                'po_date' => $result[0]->po_date,
                'po_status' => $result[0]->po_status,
                'delivery_date' => $result[0]->delivery_date,
                'supplier_code' => $result[0]->supplier_code,
                'supplier_name' => $result[0]->supplier_name,
                'supplier_address1' => $result[0]->supplier_address1,
                'supplier_address2' => $result[0]->supplier_address2,
                'supplier_city' => $result[0]->supplier_city,
                'supplier_country' => $result[0]->supplier_country,
                'sup_country_name' => $result[0]->country_description,
                'loc_name' => $result[0]->loc_name,
                'loc_address_1' => $result[0]->loc_address_1,
                'loc_address_2' => $result[0]->loc_address_2,
                'company_name' => $result[0]->company_name,
                'company_address_1' => $result[0]->company_address_1,
                'created_by' => $result[0]->first_name,
                'division' => $result[0]->division_description,
                'payment_method_description' => $result[0]->payment_method_description,
                'payment_description' => $result[0]->payment_description,
                'ship_mode' => $result[0]->ship_mode,
                'ship_term_description' => $result[0]->ship_term_description,
                'currency' => $result[0]->currency_code,
                'total_qty' => $total_qty,
                'data' => $list,
                'split' => $splitList,
                'count' => $count,
                'words' => $words,
                'summary' => $groupList
            ];
        }

        $pdf = PDF::loadView('pdf', $data);
        return $pdf->stream('document.pdf');
    }

    private function displaywords($number)
    {
        $no = (int) floor($number);
        $point = (int) round(($number - $no) * 100);
        $hundred = null;
        $digits_1 = strlen($no);
        $i = 0;
        $str = array();
        $words = array(
            '0' => '', '1' => 'one', '2' => 'two',
            '3' => 'three', '4' => 'four', '5' => 'five', '6' => 'six',
            '7' => 'seven', '8' => 'eight', '9' => 'nine',
            '10' => 'ten', '11' => 'eleven', '12' => 'twelve',
            '13' => 'thirteen', '14' => 'fourteen',
            '15' => 'fifteen', '16' => 'sixteen', '17' => 'seventeen',
            '18' => 'eighteen', '19' => 'nineteen', '20' => 'twenty',
            '30' => 'thirty', '40' => 'forty', '50' => 'fifty',
            '60' => 'sixty', '70' => 'seventy',
            '80' => 'eighty', '90' => 'ninety'
        );
        $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
        while ($i < $digits_1) {
            $divider = ($i == 2) ? 10 : 100;
            $number = floor($no % $divider);
            $no = floor($no / $divider);
            $i += ($divider == 10) ? 1 : 2;


            if ($number) {
                $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
                $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
                $str[] = ($number < 21) ? $words[$number] .
                    " " . $digits[$counter] . $plural . " " . $hundred
                    :
                    $words[floor($number / 10) * 10]
                    . " " . $words[$number % 10] . " "
                    . $digits[$counter] . $plural . " " . $hundred;
            } else $str[] = null;
        }
        $str = array_reverse($str);
        $result = implode('', $str);


        if ($point > 20) {
            $points = ($point) ?
                "" . $words[floor($point / 10) * 10] . " " .
                $words[$point = $point % 10] : '';
        } else {
            $points = $words[$point];
        }
        if ($points != '') {
            return $result . " and " . $points . "Only";
        } else {

            return $result . "Only";
        }
    }

    public function getPoColorList($id)
    {
        $poData = DB::table('merc_po_order_header as h')
            ->join('merc_po_order_details as d', 'h.po_number', '=', 'd.po_no')
            ->leftjoin('org_color as c', 'c.color_id', '=', 'd.colour')
            ->select('c.color_id', 'c.color_name')
            ->where('h.po_id', '=', $id)
            ->groupBy('d.colour')
            ->get();

        return $poData->toArray();
    }
}
