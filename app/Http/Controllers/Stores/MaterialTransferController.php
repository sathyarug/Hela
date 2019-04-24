<?php
namespace App\Http\Controllers\Stores;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;

use App\Models\Store\Stock;
use App\Models\stores\TransferLocationUpdate;
use App\models\stores\GatePassHeader;
use App\models\stores\GatePassDetails;
use App\models\store\StockTransaction;

/**
 *
 */
class MaterialTransferController extends Controller
{

  function __construct()
  {
    //add functions names to 'except' paramert to skip authentication
    $this->middleware('jwt.verify', ['except' => ['index']]);
  }


  public function index(Request $request)
  {
    $type = $request->type;

    if($type == 'datatable')   {
      $data = $request->all();
      return response($this->datatable_search($data));
    }
    else if($type == 'auto')    {
      $search = $request->search;
      return response($this->autocomplete_search($search));
    }

      else{
      $active = $request->active;
      $fields = $request->fields;
      return response([
        'data' => $this->list($active , $fields)
      ]);
    }
  }



  private function datatable_search($data)
  {
    $start = $data['start'];
    $length = $data['length'];
    $draw = $data['draw'];
    $search = $data['search']['value'];
    $order = $data['order'][0];
    $order_column = $data['columns'][$order['column']]['data'];
    $order_type = $order['dir'];

    /*$customerSizeGrid_list = CustomerSizeGrid::select('*')
    ->where('customer_id'  , 'like', $search.'%' )
    ->orderBy($order_column, $order_type)
    ->offset($start)->limit($length)->get();

    $customerSizeGrid_count = CustomerSizeGrid::where('customer_id'  , 'like', $search.'%' )
    ->count();

    return [
        "draw" => $draw,
        "recordsTotal" => $customerSizeGrid_count,
        "recordsFiltered" => $customerSizeGrid_count,
        "data" =>   $customerSizeGrid_list
    ];*/

    $gatePassDetails_list= GatePassDetails::join('store_gate_pass_header', 'store_gate_pass_details.gate_pass_id', '=', 'store_gate_pass_header.supplier_id')
    ->join('item_master','item_master.master_id','=','store_stock.item_id')
    ->join('org_color','org_color.color_id','=','store_stock.color')
    ->join('org_size','org_size.size_id','=','store_stock.size')
    ->join('org_store_bin','org_store_bin.store_bin_id','=','store_stock.bin')
    ->join('org_uom','org_uom.uom_id','=','store_stock.uom')
    ->join('store_gate_pass_details','style_creation.style_id','=','store_gate_pass_details.style_id')
    ->join('cust_customer','style_creation.customer_id','=','cust_customer.customer_id')
    ->select('','org_supplier.supplier_name', 'item_category.category_name','item_subcategory.subcategory_name','org_uom.uom_code')
    ->where('supplier_name','like',$search.'%')
    ->orWhere('category_name', 'like', $search.'%')
    ->orWhere('subcategory_name', 'like', $search.'%')
    ->orWhere('uom_code', 'like', $search.'%')
    ->orderBy($order_column, $order_type)
    ->offset($start)->limit($length)->get();

     $supplierTolarance_list_count= SupplierTolarance::join('org_supplier', 'org_supplier_tolarance.supplier_id', '=', 'org_supplier.supplier_id')
    ->join('item_category', 'org_supplier_tolarance.category_id', '=', 'item_category.category_id')
    ->join('item_subcategory', 'org_supplier_tolarance.subcategory_id', '=', 'item_subcategory.subcategory_id')
    ->join('org_uom', 'org_supplier_tolarance.uom_id', '=', 'org_uom.uom_id')
    ->select('org_supplier_tolarance.*','org_supplier.supplier_name', 'item_category.category_name','item_subcategory.subcategory_name','org_uom.uom_code')
    ->where('supplier_name','like',$search.'%')
    ->orWhere('category_name', 'like', $search.'%')
    ->orWhere('subcategory_name', 'like', $search.'%')
    ->orWhere('uom_code', 'like', $search.'%')
    ->count();
    return [
        "draw" => $draw,
        "recordsTotal" => $supplierTolarance_list_count,
        "recordsFiltered" =>$supplierTolarance_list_count,
        "data" =>$supplierTolarance_list
    ];


  }




}
