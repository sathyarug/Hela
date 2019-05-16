<?php
namespace App\Http\Controllers\Stores;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;

use App\Models\Store\Stock;
use App\Models\Store\SubStore;
use App\Models\stores\TransferLocationUpdate;
use App\Models\stores\GatePassHeader;
use App\Models\stores\GatePassDetails;
use App\Models\Store\StockTransaction;
use App\Models\Store\Store;
use App\Models\Store\StoreBin;

use Illuminate\Support\Facades\DB;
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
    else if ($type == 'getStores'){
      //$search=$request;
      //print_r($request);
      return response($this->getStores());
        //echo"im here in get Stores";
    }
    else if($type=='getSubStores'){
      return response($this->getSubStores());
    }
    else if($type=='getBins'){
      return response($this->getBins());
    }

    else if($type=='loadDetails'){
      $gatepassNo=$request->gatePassNo;
      return response(['data'=>$this->tabaleLoad($gatepassNo)]);
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



    $gatePassDetails_list= GatePassHeader::join('org_company as t', 't.company_id', '=', 'store_gate_pass_header.transfer_location')
    ->join('org_company as r', 'r.company_id', '=', 'store_gate_pass_header.receiver_location')
    ->select('store_gate_pass_header.*','t.company_name as loc_transfer','r.company_name as loc_receiver')
    ->where('gate_pass_no','like',$search.'%')
    ->orWhere('r.company_name', 'like', $search.'%')
    ->orWhere('t.company_name', 'like', $search.'%')
    ->orWhere('store_gate_pass_header.created_date', 'like', $search.'%')
    ->orderBy($order_column, $order_type)
    ->offset($start)->limit($length)->get();

     $gatePassDetails_list_count= GatePassHeader::join('org_company as t', 't.company_id', '=', 'store_gate_pass_header.transfer_location')
     ->join('org_company as r', 'r.company_id', '=', 'store_gate_pass_header.receiver_location')
     ->select('store_gate_pass_header.*','t.company_name as loc_transfer','r.company_name as loc_receiver')
     ->where('gate_pass_no','like',$search.'%')
     ->orWhere('r.company_name', 'like', $search.'%')
     ->orWhere('t.company_name', 'like', $search.'%')
     ->orWhere('store_gate_pass_header.created_date', 'like', $search.'%')
    ->count();
    return [
        "draw" => $draw,
        "recordsTotal" =>  $gatePassDetails_list_count,
        "recordsFiltered" => $gatePassDetails_list_count,
        "data" =>$gatePassDetails_list
    ];


  }

  private function tabaleLoad($gatepassNo){

$status="plan";


    $details=GatePassHeader::join('store_gate_pass_details','store_gate_pass_header.gate_pass_id','=','store_gate_pass_details.gate_pass_id')
                    ->join('merc_customer_order_details','store_gate_pass_details.customer_po_id','=','merc_customer_order_details.order_id')
                    ->join('merc_customer_order_header','merc_customer_order_details.order_id','=','merc_customer_order_header.order_id')
                    ->join('style_creation','store_gate_pass_details.style_id','=','style_creation.style_id')
                    ->join('item_master','item_master.master_id','=','store_gate_pass_details.item_id')
                    ->join('org_color','org_color.color_id','=','store_gate_pass_details.color_id')
                    ->join('org_size','org_size.size_id','=','store_gate_pass_details.size_id')
                    ->join('org_store_bin','org_store_bin.store_bin_id','=','store_gate_pass_details.bin_id')
                    ->join('org_uom','org_uom.uom_id','=','store_gate_pass_details.uom_id')
                    //->join('store_stock','store_stock.customer_po_id','=','store_gate_pass_details.customer_po_id')
                    ->select('item_master.master_code','item_master.master_id','item_master.master_description','style_creation.style_no','style_creation.style_id','store_gate_pass_details.trns_qty','merc_customer_order_header.order_code','merc_customer_order_header.order_id','org_color.color_name','org_color.color_id','org_size.size_name','org_size.size_id','org_uom.uom_code','org_uom.uom_id','store_gate_pass_header.gate_pass_id','store_gate_pass_details.customer_po_id','store_gate_pass_details.item_id')
                    //->select('*')
                    ->where('store_gate_pass_header.status','=',$status)
                    ->where('store_gate_pass_header.gate_pass_no','=',$gatepassNo)
                    ->where('store_gate_pass_header.status','=','plan');
                    //
                    //->where('store_stock.status','=',1)
                    //echo $details->toSql();

   $stockBalanceInLoction=DB::table('store_stock')
                         ->rightJoinSub($details,'gatepass_details',function($join){
                           $user = auth()->payload();
                           $user_location=$user['loc_id'];
                           //user location hardcode since db dont have real values
                        //$user_location=3;
                         $join->on('store_stock.item_id','=','gatepass_details.master_id')
                             ->on('store_stock.style_id','=','gatepass_details.style_id')
                             ->on('store_stock.size','=','gatepass_details.size_id')
                             ->on('store_stock.customer_po_id','=','gatepass_details.customer_po_id')
                             //->on('store_stock.material_code','=','item_master.master_id')
                             //->on('store_stock.color','=','gatepass_details.color_id')
                             //->select('store_stock.total_qty','store_stock.id')
                             ->select('store_stock.*')
                             ->where('store_stock.location','=',$user_location);

                        })
                    ->get();
                    return $stockBalanceInLoction;
                 //$this->setStatuszero($details);


  }

  public function getStores(){
    $user = auth()->payload();
    $user_location=$user['loc_id'];
    //$user_location=3;
   $store_list = Store::where('status',1)
                      //->where('loc_id',$user_location)
                        ->pluck('store_name')
                        ->toArray();
            return json_encode($store_list);
      //return $store_list;
  }

  public function getSubStores(){
    $user = auth()->payload();
    $user_location=$user['loc_id'];
    //$user_location=3;
    $sub_store_list=SubStore::where('status',1)
                          //->where('loc_id',$user_location)
                          ->pluck('substore_name')
                          ->toArray();
                        return json_encode($sub_store_list);

  }

  public function getBins(){
    $user = auth()->payload();
    $user_location=$user['loc_id'];
    //$user_location=3;
    $store_bin_list=StoreBin::where('status',1)
                          //->where('loc_id',$user_location)
                          ->pluck('store_bin_name')
                          ->toArray();
                        return json_encode($store_bin_list);

  }



public function storedetails (Request $request){
  $user = auth()->payload();
  $transer_location=$user['loc_id'];
  $receiver_location=$request->receiver_location;
  $gate_pass_id=$request->gate_pass_id;
  //$transer_location=3;
  $details= $request->data;
    //print_r($details);
      //print_r($gate_pass_id);*/
    for($i=0;$i<count($details);$i++){
        //save data in stock transaction table
        //get store i related to store name
         $storeName=$details[$i]["store_name"];
          $subStoreName=$details[$i]["substore_name"];
          $subStoreBin=$details[$i]["store_bin_name"];
            //$storeName="test1";
        $getStoreId=Store::where('store_name','=',$storeName)
                          ->where('loc_id','=',$transer_location)
                          ->where('status','=',1)
                          ->pluck('store_id');
                          ///$getStoreId[0];
                          //die();
                          //return $getStoreId;

        $getSubStoreId=SubStore::where('substore_name','=',$subStoreName)
                                ->where('store_id','=',$getStoreId)
                                ->where('status','=',1)
                                ->pluck('substore_id');
                                  //echo $getSubStoreId;

        $getBinId=StoreBin::where('store_id','=',$getStoreId)
                          ->where('substore_id','=',$getSubStoreId)
                          ->where('store_bin_name','=',$subStoreBin)
                          ->where('status','=',1)
                          ->pluck('store_bin_id');
                            //echo $getBinId;
     $stockTransaction=new StockTransaction();
    $stockTransaction->doc_num=$gate_pass_id;
      $stockTransaction->doc_type="GATE_PASS";
      $stockTransaction->style_id=$details[$i]["style_id"];
      $stockTransaction->size=$details[$i]["size_id"];
      $stockTransaction->customer_po_id=$details[$i]["customer_po_id"];
      $stockTransaction->item_id=$details[$i]["item_id"];
      $stockTransaction->color=$details[$i]["color_id"];
      $stockTransaction->main_store=$getStoreId[0];
      $stockTransaction->sub_store=$getSubStoreId[0];
      $stockTransaction->bin=$getBinId[0];
      $stockTransaction->uom=$details[$i]["uom_id"];
      $stockTransaction->material_code=$details[$i]["master_id"];
      $stockTransaction->location=$transer_location;
      $stockTransaction->status="RECEIVED";
      $stockTransaction->qty= $details[$i]["received_qty"];
      $stockTransaction->save();
      //update gatepass header table as RECEIVED
      $gatePassHeader= GatePassHeader::find($gate_pass_id);
      $gatePassHeader->status="RECEIVED";
      $gatePassHeader->save();
      //check current style,current size avalabale in db
        if($details[$i]["id"]!=null){
          $stockUpdate= Stock::find($details[$i]["id"]);
          $stockUpdate->total_qty=$details[$i]["total_qty"]+$details[$i]["received_qty"];
          $stockUpdate->save();

        }
        else{
        //enter the details of gate pass to the stock table
      $stockUpdate=new Stock();
      $stockUpdate->customer_po_id=$details[$i]["customer_po_id"];
      $stockUpdate->style_id=$details[$i]["style_id"];
      $stockUpdate->item_id=$details[$i]["item_id"];
      $stockUpdate->size=$details[$i]["size_id"];
      $stockUpdate->color=$details[$i]["color_id"];
      $stockUpdate->location=$transer_location;
      $stockUpdate->store=$getStoreId[0];
      $stockUpdate->sub_store=$getSubStoreId[0];
      $stockUpdate->bin=$getBinId[0];
      $stockUpdate->uom=$details[$i]["uom_id"];
      $stockUpdate->material_code=$details[$i]["master_id"];
      $stockUpdate->total_qty=$details[$i]["received_qty"];
      $stockUpdate->status=1;
      $stockUpdate->save();
      //$stockTransaction->doc_num=$gate_pass_id;
      //$stockTransaction->doc_type="GATE_PASS";

      //$stockTransaction->status="RECEIVED";
      //$stockTransaction->qty= $details[$i]["received_qty"];
      //$stockTransaction->save();


 }


}


   return response(['data'=>[
     'message'=>'Items Transferd in Successfully',

   ]

    ]

);



}



}
