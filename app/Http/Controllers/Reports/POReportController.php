<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\CapitalizeAllFields;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Merchandising\BOMDetails;
use App\Models\Core\Status;
use App\Models\Merchandising\PoOrderHeader;


class POReportController extends Controller
{ 

    public function index(Request $request)
    {
      $type = $request->type;
      if($type == 'datatable') {
          $data = $request->all();
          $this->datatable_search($data);
      }else if($type == 'header') {
          $data = $request->all();
          $this->load_po_header($data);
      }else if($type == 'auto')    {
        $search = $request->search;
        return response($this->autocomplete_search($search));
      }else {
        $active = $request->active;
        $fields = $request->fields;
        return response([
          'data' => $this->list($active , $fields)
        ]);
      }
    }

    private function list($active = 0 , $fields = null)
    {
      $query = null;
      if($fields == null || $fields == '') {
        $query = Status::select('*');
      }
      else{
        $fields = explode(',', $fields);
        $query = Status::select('*')
        ->where('type'  , 'like', '%'.$fields[1].'%' );
      }
      return $query->get();
    }

    private function autocomplete_search($search)
    {
      $po_lists = PoOrderHeader::select('po_id','po_number')
      ->where([['po_number', 'like', '%' . $search . '%'],]) ->get();
      return $po_lists;
    }

    private function datatable_search($data)
    {
      $customer = $data['data']['customer_name']['customer_id'];
      $bom_stage = $data['data']['bom_stage']['bom_stage_id'];
      $style = $data['data']['style_name']['style_no'];
      $item = $data['data']['item_name']['master_id'];
      $supplier = $data['data']['supplier_name']['supplier_id'];
      $cus_po = $data['data']['cuspo_no']['po_no'];
      $sales_order = $data['data']['salesorder_id']['order_code'];
      $pcd_from = $data['pcd_from'];
      $pcd_to = $data['pcd_to'];
      $status = $data['data']['po_status']['status'];

      $query = DB::table('bom_details')
      ->join('bom_header','bom_header.bom_id','=','bom_details.bom_id')
      ->join('merc_customer_order_details','bom_header.delivery_id','=','merc_customer_order_details.details_id')
      ->join('item_master','bom_details.master_id','=','item_master.master_id')
      ->leftJoin('org_color','bom_details.color_id','=','org_color.color_id')
      ->leftJoin('mat_ratio','bom_details.id','=','mat_ratio.bom_detail_id')
      ->leftJoin('org_size','mat_ratio.size_id','=','org_size.size_id')
      ->join('org_uom','bom_details.uom_id','=','org_uom.uom_id')
      ->leftJoin('org_supplier','bom_details.supplier_id','=','org_supplier.supplier_id')
      ->join('org_location','merc_customer_order_details.projection_location','=','org_location.loc_id')
      ->join('merc_customer_order_header','merc_customer_order_details.order_id','=','merc_customer_order_header.order_id')
      ->join('cust_customer','merc_customer_order_header.order_customer','=','cust_customer.customer_id')
      ->join('style_creation','merc_customer_order_header.order_style','=','style_creation.style_id')
      ->join('item_category','bom_details.category_id','=','item_category.category_id')
      ->join('org_origin_type','bom_details.origin_type_id','=','org_origin_type.origin_type_id')
      ->join('merc_bom_stage','merc_customer_order_header.order_stage','=','merc_bom_stage.bom_stage_id')
      ->leftJoin('org_color AS OC','mat_ratio.color_id','=','OC.color_id')
      ->select('bom_details.bom_id',
      'merc_customer_order_details.po_no AS cus_po',
      'bom_details.master_id',
      'item_master.master_description',
      'org_color.color_name AS color_name',
      'mat_ratio.color_id AS material_color_id',
      'OC.color_name AS material_color',
      'org_size.size_name AS size_name',
      'org_uom.uom_code',
      'org_supplier.supplier_name',
      'bom_details.bom_unit_price AS unit_price',
      'bom_details.moq',
      'bom_details.mcq',
      'merc_customer_order_details.pcd',
      'org_location.loc_id',
      'org_location.loc_name',
      'bom_details.id AS bom_detail_id',
      'mat_ratio.id as mat_id',
      'bom_details.color_id',
      'mat_ratio.size_id',
      'org_uom.uom_id',
      'org_supplier.supplier_id',
      'merc_customer_order_header.order_stage',
      'merc_customer_order_details.ship_mode',
      'item_category.category_name',
      'org_origin_type.origin_type',
      'org_origin_type.origin_type_id',
      'cust_customer.customer_name',
      'merc_bom_stage.bom_stage_description',
      'merc_customer_order_header.order_status',
      DB::raw("(CASE WHEN mat_ratio.required_qty IS NULL THEN bom_details.required_qty ELSE mat_ratio.required_qty END) AS order_qty"),
      DB::raw("(SELECT Sum(MPD.req_qty)AS received_qty
          FROM merc_po_order_details AS MPD
          WHERE
          MPD.bom_id = bom_details.bom_id AND
          MPD.bom_detail_id = bom_details.id AND
          (MPD.mat_id IS NULL OR MPD.mat_id = mat_ratio.id OR
          MPD.size = mat_ratio.size_id OR MPD.mat_colour = mat_ratio.color_id)) AS received_qty"),
      DB::raw("(SELECT GROUP_CONCAT( DISTINCT MPOD.po_no SEPARATOR ' | ' )AS po_nos
          FROM merc_po_order_details AS MPOD WHERE
          MPOD.bom_id = bom_details.bom_id AND
          MPOD.bom_detail_id = bom_details.id AND
          (MPOD.mat_id IS NULL OR MPOD.mat_id = mat_ratio.id OR
          MPOD.size = mat_ratio.size_id OR MPOD.mat_colour = mat_ratio.color_id)) AS po_nos"),
      DB::raw("(SELECT
          Count(EX.currency) AS ex_rate
          FROM
          org_exchange_rate AS EX
          WHERE
          EX.currency = org_supplier.currency) AS ex_rate")
      );
      if($customer!=null || $customer!=""){
        $query->where('cust_customer.customer_id', $customer);
      }
      if($bom_stage!=null || $bom_stage!=""){
        $query->where('merc_customer_order_header.order_stage', $bom_stage);
      }
      if($style!=null || $style!=""){
        $query->where('style_creation.style_no', $style);
      }
      if($item!=null || $item!=""){
        $query->where('bom_details.master_id', $item);
      }
      if($supplier!=null || $supplier!=""){
        $query->where('bom_details.supplier_id', $supplier);
      }
      if($cus_po!=null || $cus_po!=""){
        $query->where('merc_customer_order_details.po_no', $cus_po);
      }
      if($sales_order!=null || $sales_order!=""){
        $query->where('merc_customer_order_header.order_code', $sales_order);
      }
      if($pcd_from!=null || $pcd_from!=""){
        $query->whereBetween('merc_customer_order_details.pcd', [date("Y-m-d",strtotime($pcd_from)), date("Y-m-d",strtotime($pcd_to))]);
      }
      if($status!=null || $status!=""){
        $query->where('merc_customer_order_header.order_status', $status);
      }
      $load_list = $query->get();

      echo json_encode([
          "recordsTotal" => "",
          "recordsFiltered" => "",
          "data" => $load_list
      ]);

  }

  private function load_po_header($data)
  {
      $po_no = $data['data']['po_no']['po_number'];
      $supplier = $data['data']['supplier_name']['supplier_id'];
      $po_from = $data['po_from'];
      $po_to = $data['po_to'];
      $status = $data['data']['po_status']['status'];

      $query = DB::table('merc_po_order_header')
      ->join('org_supplier','merc_po_order_header.po_sup_code','=','org_supplier.supplier_id')
      ->join('usr_login','merc_po_order_header.created_by','=','usr_login.user_id')
      ->join('org_location','merc_po_order_header.user_loc_id','=','org_location.loc_id')
      ->select('merc_po_order_header.*',
      'org_supplier.supplier_name',
      'usr_login.user_name',
      DB::raw("(SELECT
        FORMAT(SUM(merc_po_order_details.tot_qty),2)
        FROM
        merc_po_order_details
        WHERE
        merc_po_order_details.po_header_id = merc_po_order_header.po_id) AS total_amount"),
      'org_location.loc_name'
      );

      if($po_no!=null || $po_no!=""){
        $query->where('merc_po_order_header.po_number', $po_no);
      }
      if($supplier!=null || $supplier!=""){
        $query->where('merc_po_order_header.po_sup_code', $supplier);
      }
      if($po_from!=null || $po_from!=""){
        $query->whereBetween('merc_po_order_header.po_date', [date("Y-m-d",strtotime($po_from)), date("Y-m-d",strtotime($po_to))]);        
      }
      if($status!=null || $status!=""){
        $query->where('merc_po_order_header.po_status', $status);
      }
      $load_list = $query->get();

      echo json_encode([
          "recordsTotal" => "",
          "recordsFiltered" => "",
          "data" => $load_list
      ]);

  }


}
