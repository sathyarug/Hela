<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\CapitalizeAllFields;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Core\Status;
use PDF;

class PickListController extends Controller
{ 

    public function index(Request $request)
    {
      $type = $request->type;
      if($type == 'datatable') {
          $data = $request->all();
          $this->datatable_search($data);
      }else {
        $active = $request->active;
        $fields = $request->fields;
        return response([
          'data' => $this->list($active , $fields)
        ]);
      }
    }

    public function datatable_search($data)
    {
      $mrn = $data['data']['mrn_no']['mrn_id'];
      $customer = $data['data']['customer_name']['customer_id'];
      $style = $data['data']['style_name']['style_id'];
      $location = $data['data']['loc_name']['loc_id'];
      $date_from = $data['date_from'];
      $date_to = $data['date_to'];

      $query = DB::table('store_issue_header')
      ->join('store_mrn_header','store_issue_header.mrn_id','=','store_mrn_header.mrn_id')
      ->join('style_creation','store_mrn_header.style_id','=','style_creation.style_id')
      ->join('cust_customer','style_creation.customer_id','=','cust_customer.customer_id')
      ->join('usr_login','store_issue_header.created_by','=','usr_login.user_id')
      ->join('org_location','store_issue_header.user_loc_id','=','org_location.loc_id')
      ->select('store_issue_header.issue_no',
        'store_mrn_header.mrn_id',
        'store_mrn_header.mrn_no',
        'store_mrn_header.cut_qty',
        DB::raw("(DATE_FORMAT(store_issue_header.created_date,'%d-%b-%Y')) AS created_date"),
        'cust_customer.customer_name',
        'style_creation.style_no',
        'usr_login.user_name',
        'org_location.loc_name'
      );
      if($mrn!=null || $mrn!=""){
        $query->where('store_mrn_header.mrn_id', $mrn);
      }
      if($customer!=null || $customer!=""){
        $query->where('cust_customer.customer_id', $customer);
      }
      if($style!=null || $style!=""){
        $query->where('style_creation.style_id', $style);
      }
      if($location!=null || $location!=""){
        $query->where('store_mrn_header.user_loc_id', $location);
      }
      if($date_from!=null || $date_from!=""){
        $query->whereBetween(DB::raw('(DATE_FORMAT(store_mrn_header.created_date,"%Y-%m-%d"))'),[date("Y-m-d",strtotime($date_from)), date("Y-m-d",strtotime($date_to))]);
      }
      $query->orderBy('store_mrn_header.mrn_id','DESC' , 'store_issue_header.issue_no','DESC');
      $data = $query->get();

      echo json_encode([
        "recordsTotal" => "",
        "recordsFiltered" => "",
        "data" => $data
      ]);

    }

    public function viewPickList(Request $request)
    {

      $mrn=$request->mrn;
      $issue_no=$request->issue;

      $data['company'] = $query = DB::table('store_mrn_header')
      ->join('org_location','store_mrn_header.user_loc_id','=','org_location.loc_id')
      ->join('org_company', 'org_location.company_id', '=', 'org_company.company_id')
      ->join('org_country', 'org_location.country_code', '=', 'org_country.country_id')
      ->select('org_location.*','org_company.company_name','org_country.country_description')
      ->where('store_mrn_header.mrn_id','=',$mrn)
      ->get();

      $data['headers'] = $query = DB::table('store_issue_header')
      ->join('store_mrn_header','store_issue_header.mrn_id','=','store_mrn_header.mrn_id')
      ->join('style_creation','store_mrn_header.style_id','=','style_creation.style_id')
      ->select('store_mrn_header.line_no',
        'style_creation.style_no',
        'store_mrn_header.cut_qty',
        'store_mrn_header.mrn_id',
        'store_mrn_header.mrn_no',
        DB::raw("(SELECT
        item_master.master_code
        FROM
        store_mrn_detail
        INNER JOIN merc_shop_order_header ON store_mrn_detail.shop_order_id = merc_shop_order_header.shop_order_id
        INNER JOIN item_master ON merc_shop_order_header.fg_id = item_master.master_id
        WHERE
        store_mrn_detail.mrn_id = store_mrn_header.mrn_id
        GROUP BY
        item_master.master_code) AS fg_code"),
        DB::raw("(SELECT
        GROUP_CONCAT(DISTINCT store_mrn_detail.cust_order_detail_id SEPARATOR ' / ')
        FROM
        store_mrn_detail
        WHERE
        store_mrn_detail.mrn_id = store_mrn_header.mrn_id) AS cust_order"),
        DB::raw("(SELECT
        GROUP_CONCAT(DISTINCT merc_po_order_details.po_no SEPARATOR ' / ') AS po_nos
        FROM
        store_mrn_detail
        INNER JOIN merc_po_order_details ON store_mrn_detail.shop_order_detail_id = merc_po_order_details.shop_order_detail_id
        WHERE
        store_mrn_detail.mrn_id = store_mrn_header.mrn_id
        ) AS po_nos"),
        'store_issue_header.issue_id'
      )
      ->where('store_issue_header.mrn_id','=',$mrn)
      ->where('store_issue_header.issue_id','=',$issue_no)
      ->get();

      $data['details'] = $query = DB::table('store_issue_header')
      ->join('store_issue_detail','store_issue_header.issue_id','=','store_issue_detail.issue_id')
      ->join('item_master','store_issue_detail.item_id','=','item_master.master_id')
      ->join('org_location','store_issue_detail.location_id','=','org_location.loc_id')
      ->join('org_store','store_issue_detail.store_id','=','org_store.store_id')
      ->join('org_substore','store_issue_detail.sub_store_id','=','org_substore.substore_id')
      ->join('org_store_bin','store_issue_detail.bin','=','org_store_bin.store_bin_id')
      ->join('store_mrn_detail','store_issue_detail.mrn_detail_id','=','store_mrn_detail.mrn_detail_id')
      ->leftJoin('org_size','store_mrn_detail.size_id','=','org_size.size_id')     
      ->select('store_issue_header.issue_id',
        'item_master.master_code',
        'item_master.master_description',
        'org_location.loc_name',
        'org_store.store_name',
        'org_substore.substore_name',
        'org_store_bin.store_bin_name',
        'org_size.size_name',
        'store_mrn_detail.order_qty',
        'store_mrn_detail.required_qty',
        'store_mrn_detail.requested_qty',
        'store_mrn_detail.total_qty',
        'store_issue_detail.qty AS issue_qty'
      )
      ->where('store_issue_header.mrn_id','=',$mrn)
      ->where('store_issue_header.issue_id','=',$issue_no)
      ->get();
   
      $config = [
        'format' => 'A4',
        'orientation' => 'L', //L-landscape
        //'watermark' => '',
        //'show_watermark' => true,
      ];

      $pdf = PDF::loadView('reports/pick-list', $data, [], $config)
      ->stream('Pick List -'.$request->mrn.'.pdf');
      return $pdf;

      //return View('reports/pick-list',$data);

    }
    

}
