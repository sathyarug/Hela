<?php

namespace App\Http\Controllers\Merchandising;

use App\Models\Merchandising\BOMHeader;
use App\Models\Merchandising\BOMDetails;
use App\Models\Merchandising\CustomerOrder;
use App\Models\Merchandising\CustomerOrderDetails;
use App\Models\Merchandising\Costing\Costing;
use App\Models\Merchandising\Costing\CostingFinishGoodComponentItem;
use App\Models\Merchandising\BOMSOAllocation;
use App\Models\Merchandising\MaterialRatio;

use App\Models\Merchandising\StyleCreation;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class BomController extends Controller
{
    public function index(Request $request)
    {
      $type = $request->type;

      if($type == 'header_data') {//return bom header data
        return response([
          'data' => $this->get_header_data($request->costing_id)
        ]);
      }
      else if($type == 'items') {
        return response([
          'items' => $this->get_items($request->delivery_id)
        ]);
      }
      else if($type == 'bom_item_details') {
        return response([
          'data' => $this->get_bom_item_details($request->bom_detail_id)
        ]);
      }
      else if($type == 'datatable') {
          $data = $request->all();
          $this->datatable_search($data);
      }
    }


    public function store(Request $request)
    {
        $items = $request->items;
        $status = true;
        for($x = 0 ; $x < sizeof($items) ; $x++){
          $bom_item = BOMDetails::find($items[$x]['id']);
          $size_wise_count = DB::table('mat_ratio')->where('bom_detail_id', '=', $bom_item->id)->sum('required_qty');
          if($items[$x]['required_qty'] < $size_wise_count) {
            $status = false;
            break;
          }
          else {
            $bom_item->required_qty = $items[$x]['required_qty'];
            $bom_item->bom_unit_price = $items[$x]['bom_unit_price'];
            $bom_item->total_cost = ($bom_item->gross_consumption * $bom_item->required_qty * $bom_item->bom_unit_price) + $bom_item->freight_cost + $bom_item->surcharge;
            $bom_item->save();
          }
        }

        if($status == true) {
          $bom = BomHeader::find($items[0]['bom_id']);
          return response([
            'data' => [
              'status' => 'success',
              'message' => 'Item saved successfully',
              'items' => $this->get_items($bom->delivery_id)
            ]
          ]);
        }
        else {
          return response([
            'data' => [
              'status' => 'error',
              'message' => 'Size wise qty is grater than enterd required qty in line ' . ($x + 1)
            ]
          ]);
        }
    }


    public function show($id)
    {
      $bom = BomHeader::find($id);
      $header_data = $this->get_header_data($bom->costing_id);
      $items = $this->get_items($bom->delivery_id);
      return [
        'data' => [
          'bom' => $bom,
          'header_data' => $header_data,
          'items' => $items
        ]
      ];
    }


    public function edit(bom_details $bom_details)
    {
        //
    }


    public function update(Request $request, bom_details $bom_details)
    {
        //
    }


    public function destroy(bom_details $bom_details)
    {
        //
    }

    /*public function getCustOrders(Request $request){
        $customerOrder = new CustomerOrder();
        //$rsCustOrderList = $customerOrder->getCustomerOrders($request->costingId);
        $rsCustOrderList = $customerOrder->getCustomerOrders($request->costingId, $request->colorComboID);

        echo json_encode($rsCustOrderList);
    }*/

    /*public function getAssignCustOrders(Request $request){
        $customerOrder = new CustomerOrder();
        $rsCustOrderList = $customerOrder->getAssignCustomerOrders($request->costingId);

        echo json_encode($rsCustOrderList);
    }*/

    /*public function getCustomerOrderQty(Request $request){

        $customerOrderDetails = new CustomerOrderDetails();
        $rsCustomerOrderQty = $customerOrderDetails->getCustomerOrderQty($request->orderId, $request->colorcomboid);

        echo json_encode($rsCustomerOrderQty);
    }*/

    /*public function getCostingRMDetails(Request $request){
        $bulkCostingDetails = new BulkCostingDetails();
        $rsRMDetails = $bulkCostingDetails->getCostingItemDetails($request->costingId);

        echo json_encode($rsRMDetails);
    }*/

    /*public function saveBOMHeader(Request $request){

        try{

            $bomHeader = new BOMHeader();
            $bomHeader->costing_id = $request->costingid;
            $bomHeader->color_combo = $request->colorComboId;

            $bomHeader->saveOrFail();

            //Get last inserted BOM ID
            $bomID = $bomHeader->bom_id;

        }catch ( \Exception $ex) {

            $bomID = "fail";
        }

        echo json_encode(array('bomid'=>$bomID));
    }*/

    /*public function saveBOMDetails(Request $request){

        $bomDeatils = new bom_details();
        $bomDeatils->bom_id = $request->bomid;
        $bomDeatils->master_id = $request->itemcode;
        $bomDeatils->item_color =  $request->itemcolor;
        $bomDeatils->uom_id = $request->uomid;
        $bomDeatils->unit_price = $request->unitprice;
        $bomDeatils->conpc = $request->conpc;
        $bomDeatils->total_qty = $request->totreqqty;
        $bomDeatils->total_value = $request->totvalue;
        $bomDeatils->artical_no = $request->articalno;
        $bomDeatils->status = 1;
        $bomDeatils->bal_qty = $request->totreqqty;
        $bomDeatils->item_size = $request->itemsize;
        $bomDeatils->component_id = $request->componentid;
        $bomDeatils->supplier_id = $request->supplierid;
        $bomDeatils->item_wastage = $request->wastage;
        $bomDeatils->combine_id = $request->combineid;

        $bomDeatils->saveOrFail();
    }*/

    /*public function saveSOAllocation(Request $request){

        try{

            $bomSOAllocation = new BOMSOAllocation();

            $bomSOAllocation->costing_id = $request->costing_id;
            $bomSOAllocation->order_id = $request->order_id;
            $bomSOAllocation->bom_id = $request->bom_id;

            $bomSOAllocation->saveOrFail();

            $status = "success";

        }catch ( \Exception $ex) {

            $status = "fail";
        }
        echo json_encode(array('status'=>$status));
    }*/

    /*public function validateBOMExist(Request $request){



    }*/

    /*public function ListBOMS(Request $request){
        try{

            $result = BOMHeader::select(DB::raw("*, CONCAT('B',LPAD(bom_id,6,'0')) AS BomNo"))->where("costing_id",$request->costing_id)->get();
        }catch( \Exception $ex){
            $result = "fail";
        }

        echo json_encode($result);
    }*/

    /*public function getBOMOrderQty(Request $request){

        try{

            $bomHeader = new BOMHeader();
            $result = $bomHeader->getBOMOrderQty($request->bomId);


        }catch( \Exception $ex){
            $result = $ex->getMessage();
        }

        echo json_encode($result);
    }*/

    /*public function getBOMDetails(Request $request){

        try{

            $bomDeatils = new bom_details();
            $result = $bomDeatils->GetBOMDetails($request->bomId);

        }catch ( \Exception $ex){
            $result = $ex->getMessage();
        }

        echo json_encode($result);
    }*/

    /*public function getSizeWiseDetails(Request $request){

        try{

            $customerOrderDetails = new CustomerOrderDetails();
            $result = $customerOrderDetails->getCustomerOrderSizes($request->orderId);

        }catch( \Exception $ex){
            $result = $ex->getMessage();
        }

        echo json_encode($result);
    }*/

    /*public function getColorWiseDetails(Request $request){
        try{
            $customerOrderDetails = new CustomerOrderDetails();
            $result = $customerOrderDetails->getCustomerColors($request->orderId);

        }catch( \Exception $ex){
            $result = $ex->getMessage();
        }
        echo json_encode($result);
    }*/

    /*public function getRatioDetails(Request $request){
        try{
            $customerOrderDetails = new CustomerOrderDetails();
            $result = $customerOrderDetails->getCustomerColorsAndSizes($request->orderId);

        }catch( \Exception $ex){
            $result = $ex->getMessage();
        }
        echo json_encode($result);
    }*/

    /*public function clearMatRatio(Request $request){

        try{

            $materialRatio = new MaterialRatio();
            if(MaterialRatio::where('bom_id','=',$request->bom_id)->where('component_id','=',$request->component_id)->where('master_id','=',$request->master_id)->exists()){
                MaterialRatio::where('bom_id','=',$request->bom_id)
                            ->where('component_id','=',$request->component_id)
                            ->where('master_id','=',$request->master_id)
                            ->update(['status'=>'0']);
            }


        } catch ( \Exception $ex) {

        }
    }*/

  /*  public function saveMaterialRatio(Request $request){

        try{

            $materialRatio = new MaterialRatio();
           // $res = MaterialRatio::where('bom_id','=',$request->bom_id)->where('component_id','=',$request->component_id)->where('master_id','=',$request->master_id)->where('color_id','=',$request->color_id)->where('size_id','=',$request->size_id)->exists();


            if(MaterialRatio::where('bom_id','=',$request->bom_id)->where('component_id','=',$request->component_id)->where('master_id','=',$request->master_id)->where('color_id','=',$request->color_id)->where('size_id','=',$request->size_id)->exists()){
               // $materialRatio->required_qty    = $request->required_qty;
                MaterialRatio::where('bom_id','=',$request->bom_id)
                            ->where('component_id','=',$request->component_id)
                            ->where('master_id','=',$request->master_id)
                            ->where('color_id','=',$request->color_id)
                            ->where('size_id','=',$request->size_id)
                            ->update(['required_qty'=>$request->required_qty,'status'=>'1']);

            }else{

                $materialRatio->bom_id          = $request->bom_id;
                $materialRatio->component_id    = $request->component_id;
                $materialRatio->master_id       = $request->master_id;
                $materialRatio->color_id        = $request->color_id;
                $materialRatio->size_id         = $request->size_id;
                $materialRatio->required_qty    = $request->required_qty;
                $materialRatio->order_id        = $request->orderid;
                $materialRatio->status          = '1';
                $materialRatio->saveOrFail();

            }



            $result = "Ratio Saved";

        } catch ( \Exception $ex) {
            $result = $ex->getMessage();
        }

        echo json_encode($result);
    }*/

    /*public function getColorCombo(Request $request){
        try{
            $bomHeader = new BOMHeader();
            $result = $bomHeader->getColorCombpoByCosting($request->costing_id);

        }catch( \Exception $ex){
            $result = $ex->getMessage();
        }
        echo json_encode($result);

    }*/

    /*public function getMatRatio(Request $request){

        try{
            $getMaterialRatio = new MaterialRatio();
            $resultMaterialRatio = $getMaterialRatio->getMaterialRatio($request->bom_id,$request->component_id,$request->item_id);

        }catch( \Exception $ex){
            $resultMaterialRatio = $ex->getMessage();
        }
        echo json_encode($resultMaterialRatio);
    }*/

    /*public function getAssignSalesOrder(Request $request){
        try{
            $SOAllocation = BOMSOAllocation::select("order_id")->where("bom_id",$request->bomId)->get();

        }catch( \Exception $ex){
            $SOAllocation = $ex->getMessage();
        }
        echo json_encode($SOAllocation);

    }*/


//******************************************************

 public function saveMeterialRatio(Request $request){
   $ratio = $request->ratio;
   $bom_detail = BOMDetails::find($request->bom_detail_id);
   //must check ratio is used in purchase order
   MaterialRatio::where('bom_detail_id', '=', $bom_detail->id)->delete();

   for($x = 0 ; $x < sizeof($ratio) ; $x++){
     $mat_ratio = new MaterialRatio();
     $mat_ratio->bom_id = $bom_detail->bom_id;
     $mat_ratio->bom_detail_id = $bom_detail->id;
     $mat_ratio->color_id = $ratio[$x]['color_id'];
     $mat_ratio->size_id = $ratio[$x]['size_id'];
     $mat_ratio->required_qty = $ratio[$x]['required_qty'];
     $mat_ratio->status = 1;
     $mat_ratio->save();
   }

   return response([
     'data' => [
       'status' => 'success',
       'message' => 'Material ratio saved successfully',
       'ratio' => $this->get_mat_ratio($bom_detail->id)
     ]
   ]);
 }







//*******************************************************


private function get_header_data($costing_id){
  $costing = Costing::with(['bom_stage', 'season', 'color_type'])->find($costing_id);
  $style = StyleCreation::with(['customer', 'division'])->find($costing->style_id);
  $deliveries = $this->get_costing_connected_deliveries($costing_id);
  return [
    'style_id' => $style->style_id,
    'style_no' => $style->style_no,
    'style_description' => $style->style_description,
    'bom_stage_id' => $costing->bom_stage->bom_stage_id,
    'bom_stage_description' => $costing->bom_stage->bom_stage_description,
    'season_id' => $costing->season->season_id,
    'season_name' => $costing->season->season_name,
    'col_opt_id' => $costing->color_type->col_opt_id,
    'color_option' => $costing->color_type->color_option,
    'customer_id' => $style->customer->customer_id,
    'customer_name' => $style->customer->customer_name,
    'division_id' => $style->division->division_id,
    'division_description' => $style->division->division_description,
    'order_code' => (sizeof($deliveries) > 0) ? $deliveries[0]->order_code : '',
    'deliveries' => $deliveries
  ];
}


private function get_costing_connected_deliveries($costing_id){
    $list = CustomerOrderDetails::select('merc_customer_order_details.details_id', 'merc_customer_order_details.line_no',
    'merc_customer_order_header.order_code', 'merc_customer_order_details.po_no', 'merc_customer_order_details.planned_qty',
    'merc_customer_order_details.order_qty',"org_country.country_description", "merc_customer_order_details.cus_style_manual")
    ->join('merc_customer_order_header', 'merc_customer_order_header.order_id', '=', 'merc_customer_order_details.order_id')
    ->join('org_country', 'org_country.country_id', '=', 'merc_customer_order_details.country')
    ->where('merc_customer_order_details.costing_id', '=', $costing_id)->where('merc_customer_order_details.delivery_status', '=', 'CONNECTED')
    ->get();
    return $list;
}


private function get_items($delivery_id) {
  $delivery = CustomerOrderDetails::find($delivery_id);

  $items = BOMDetails::join('bom_header', 'bom_header.bom_id', '=', 'bom_details.bom_id')
  ->join('costing_finish_good_components', 'costing_finish_good_components.id', '=', 'bom_details.fg_component_id')
  ->join('product_component', 'product_component.product_component_id', '=', 'costing_finish_good_components.product_component_id')
  ->leftjoin('item_category', 'item_category.category_id', '=', 'bom_details.category_id')
  ->leftjoin('item_master', 'item_master.master_id', '=', 'bom_details.master_id')
  ->leftjoin('merc_position', 'merc_position.position_id', '=', 'bom_details.position_id')
  ->leftjoin('org_uom', 'org_uom.uom_id', '=', 'bom_details.uom_id')
  ->leftjoin('org_color', 'org_color.color_id', '=', 'bom_details.color_id')
  ->leftjoin('org_supplier', 'org_supplier.supplier_id', '=', 'bom_details.supplier_id')
  ->leftjoin('org_origin_type', 'org_origin_type.origin_type_id', '=', 'bom_details.origin_type_id')
  ->leftjoin('org_garment_options', 'org_garment_options.garment_options_id', '=', 'bom_details.garment_options_id')
  ->leftjoin('fin_shipment_term', 'fin_shipment_term.ship_term_id', '=', 'bom_details.ship_term_id')
  ->leftjoin('org_country', 'org_country.country_id', '=', 'bom_details.country_id')
  ->select('bom_details.*', 'item_category.category_name', 'item_master.master_description','item_master.subcategory_id', 'merc_position.position',
      'org_uom.uom_code', 'org_color.color_name', 'org_supplier.supplier_name', 'org_origin_type.origin_type',
      'org_garment_options.garment_options_description', 'fin_shipment_term.ship_term_description', 'org_country.country_description',
      'product_component.product_component_description','product_component.product_component_id',
      DB::raw('false as edited')
   )->where('bom_header.delivery_id', '=', $delivery->details_id)->get();
  return $items;
}


private function generate_bom_for_delivery($delivery_id) {
  $delivery = CustomerOrderDetails::find($delivery_id);
  //$costing_finisg_good = CostingFinishGood::find($delivery->fg_id);

  $bom = new BomHeader();
  $bom->costing_id = $delivery->costing_id;
  $bom->delivery_id = $delivery->details_id;
  $bom->save();

  $components = CostingFinishGoodComponent::where('fg_id', '=', $delivery->fg_id)->get()->pluck('id');
  $items = CostingFinishGoodComponentItem::whereIn('fg_component_id', $components)->get();
  $items = json_decode(json_encode($items)); //conver to array
  for($x = 0 ; $x < sizeof($items); $x++) {
    $items[$x]['bom_id'] = $bom->bom_id;
    $items[$x]['costing_item_id'] = $items[$x]['id'];
    $items[$x]['id'] = 0; //clear id of previous data, will be auto generated
    $items[$x]['created_date'] = null;
    $items[$x]['created_by'] = null;
    $items[$x]['updated_date'] = null;
    $items[$x]['updated_by'] = null;
  }
  DB::table('bom_details')->insert($items);
}


private function get_mat_ratio($bom_detail_id){
  $ratio = MaterialRatio::select('mat_ratio.*', 'org_size.size_name', 'org_color.color_name')
  ->leftjoin('org_size', 'org_size.size_id', '=', 'mat_ratio.size_id')
  ->leftjoin('org_color', 'org_color.color_id', '=', 'mat_ratio.color_id')
  ->where('mat_ratio.bom_detail_id', '=', $bom_detail_id)->get();
  return $ratio;
}


private function get_bom_item_details($bom_detail_id){
  $bom_item = BOMDetails::find($bom_detail_id);
  $ratio = $this->get_mat_ratio($bom_detail_id);
  return [
    'bom_item' => $bom_item,
    'ratio' => $ratio
  ];
}


private function datatable_search($data){
  $start = $data['start'];
  $length = $data['length'];
  $draw = $data['draw'];
  $search = $data['search']['value'];
  $order = $data['order'][0];
  $order_column = $data['columns'][$order['column']]['data'];
  $order_type = $order['dir'];

  $bom_list = BomHeader::select('bom_header.*','style_creation.style_no','merc_bom_stage.bom_stage_description',
    'org_season.season_name', 'merc_color_options.color_option', 'merc_customer_order_details.line_no',
    'merc_customer_order_details.order_qty')
  ->join('costing', 'costing.id', '=', 'bom_header.costing_id')
  ->join('style_creation', 'style_creation.style_id', '=', 'costing.style_id')
  ->join('merc_bom_stage', 'merc_bom_stage.bom_stage_id', '=', 'costing.bom_stage_id')
  ->join('org_season', 'org_season.season_id', '=', 'costing.season_id')
  ->join('merc_color_options', 'merc_color_options.col_opt_id', '=', 'costing.color_type_id')
  ->join('merc_customer_order_details', 'merc_customer_order_details.details_id', '=', 'bom_header.delivery_id')
  ->where('bom_header.bom_id'  , 'like', $search.'%' )
  ->orWhere('bom_header.sc_no'  , 'like', $search.'%' )
  ->orWhere('style_creation.style_no'  , 'like', $search.'%' )
  ->orWhere('merc_bom_stage.bom_stage_description','like',$search.'%')
  ->orWhere('org_season.season_name','like',$search.'%')
  ->orWhere('merc_color_options.color_option','like',$search.'%')
  ->orderBy($order_column, $order_type)
  ->offset($start)->limit($length)->get();

  $bom_count = BomHeader::join('costing', 'costing.id', '=', 'bom_header.costing_id')
  ->join('style_creation', 'style_creation.style_id', '=', 'costing.style_id')
  ->join('merc_bom_stage', 'merc_bom_stage.bom_stage_id', '=', 'costing.bom_stage_id')
  ->join('org_season', 'org_season.season_id', '=', 'costing.season_id')
  ->join('merc_color_options', 'merc_color_options.col_opt_id', '=', 'costing.color_type_id')
  ->join('merc_customer_order_details', 'merc_customer_order_details.details_id', '=', 'bom_header.delivery_id')
  ->where('bom_header.bom_id'  , 'like', $search.'%' )
  ->orWhere('bom_header.sc_no'  , 'like', $search.'%' )
  ->orWhere('style_creation.style_no'  , 'like', $search.'%' )
  ->orWhere('merc_bom_stage.bom_stage_description','like',$search.'%')
  ->orWhere('org_season.season_name','like',$search.'%')
  ->orWhere('merc_color_options.color_option','like',$search.'%')
  ->count();

  echo json_encode([
      "draw" => $draw,
      "recordsTotal" => $bom_count,
      "recordsFiltered" => $bom_count,
      "data" => $bom_list
  ]);
}


}
