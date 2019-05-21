<?php
namespace App\Http\Controllers\Stores;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use App\Models\Merchandising\CustomerOrder;
use App\Models\Merchandising\CustomerOrderDetails;
use App\Models\Merchandising\styleCreation;
use App\Models\Finance\Item\SubCategory;
use App\Models\Store\Stock;
use App\Models\stores\TransferLocationUpdate;
use App\models\stores\GatePassHeader;
use App\models\stores\GatePassDetails;
use App\models\store\StockTransaction;



 class TransferLocationController extends Controller{



   public function __construct()
   {
     //add functions names to 'except' paramert to skip authentication
     $this->middleware('jwt.verify', ['except' => ['index']]);
   }


   //get customer size list
   public function index(Request $request)
   {
     $type = $request->type;

     if($type == 'style')   {
       $searchFrom = $request->searchFrom;
       $searchTo=$request->searchTo;
       return response($this->styleFromSearch($searchFrom, $searchTo));
     }
/*     else if($type=='saveDetails'){
       $details=$request->details;
       print_r($details);


     }*/
    else if($type=='loadDetails'){
       $style=$request->searchFrom;
       //print_r($request->searchFrom);
       return response(['data'=>$this->tabaleLoad($style)]);

     }


     else if ($type == 'auto')    {
       $search = $request->search;
       return response($this->autocomplete_search($search));
     }

   else{
       $active = $request->active;
       $fields = $request->fields;
       return null;
     }
   }


    private function styleFromSearch($searchFrom, $searchTo){

   $stylefrom=CustomerOrder::join('style_creation','merc_customer_order_header.order_style','=','style_creation.style_id')
                          //->join('merc_customer_order_details','merc_cutomer_order_header.order_id','=','merc_customer_order_details.order_id')
                          ->select('style_creation.style_no')
                          ->where('merc_customer_order_header.order_code','=',$searchFrom)
                          ->where('style_creation.status','=',1)
                          ->first();
  $styleTo=CustomerOrder::join('style_creation','merc_customer_order_header.order_style','=','style_creation.style_id')
                     //->join('merc_customer_order_details','merc_cutomer_order_header.order_id','=','merc_customer_order_details.order_id')
                         ->select('style_creation.style_no')
                         ->where('merc_customer_order_header.order_code','=',$searchTo)
                         ->where('style_creation.status','=',1)
                         ->first();



                          return [
                            "styleFrom"=>$stylefrom,
                            "styleTo"=>$styleTo

                            ];


                            }


                      private function tabaleLoad($style){

                        $user = auth()->payload();
                        $user_location=$user['loc_id'];





                        $details=Stock::join('style_creation','store_stock.style_id','=','style_creation.style_id')
                                        ->join('item_master','item_master.master_id','=','store_stock.item_id')
                                        ->join('org_color','org_color.color_id','=','store_stock.color')
                                        ->join('org_size','org_size.size_id','=','store_stock.size')
                                        ->join('org_store_bin','org_store_bin.store_bin_id','=','store_stock.bin')
                                        ->join('org_uom','org_uom.uom_id','=','store_stock.uom')
                                        ->select('item_master.master_code','item_master.master_description','org_color.color_name','org_size.size_name','org_store_bin.store_bin_name','org_uom.uom_code','store_stock.total_qty','store_stock.id')
                                        //->select('*')
                                        ->where('style_creation.style_no','=',$style)
                                        ->where('store_stock.location','=',$user_location)
                                        ->where('store_stock.status','=',1)
                                        ->get();
                                        $this->setStatuszero($details);
                                        return $details;

                      }

                      private function setStatuszero($details){
                        for($i=0;$i<count($details);$i++){
                          $id=$details[$i]["id"];
                          $setStatusZero=TransferLocationUpdate::find($id);
                          $setStatusZero->status=0;
                          $setStatusZero->save();


                        }



                      }

                      public function storedetails (Request $request){
                        $user = auth()->payload();
                        $transer_location=$user['loc_id'];
                        $receiver_location=$request->receiver_location;
                        //print_r($receiver_location);
                          $id;
                          $qty;
                        $details= $request->data;
                       for($i=0;$i<count($details);$i++){
                              $status="";
                              $id=$details[$i]["id"];
                              $gatePassHeader=new GatePassHeader();

                              $stockUpdateDetails= TransferLocationUpdate::find($id);
                              if($details[$i]["trns_qty"]!=0){
                              $status="transfer";
                              }
                              $qty=$details[$i]["total_qty"]-$details[$i]["trns_qty"];

                            $stockUpdateDetails->transfer_status=$status;
                            $stockUpdateDetails->total_qty=$qty;
                            $stockUpdateDetails->status=1;
                            $stockUpdateDetails->save();

                          }
                            //$gatePassHeader->id=$id;
                            $gatePassHeader->transfer_location=$transer_location;
                            $gatePassHeader->receiver_location=$receiver_location;
                            $gatePassHeader->status="plan";
                            $gatePassHeader->save();
                            $gate_pass_id=$gatePassHeader->gate_pass_id;
                            //print_r($gate_pass_id);*/
                            for($i=0;$i<count($details);$i++){
                            $id=$details[$i]["id"];
                            $stockUpdateDetails= TransferLocationUpdate::find($id);
                            $gatePassDetails= new GatePassDetails();
                            $stockTransaction=new StockTransaction();
                            if($stockUpdateDetails->transfer_status=="transfer"){
                            $gatePassDetails->gate_pass_id=$gate_pass_id;
                            $gatePassDetails->size_id=$stockUpdateDetails->size;
                            $gatePassDetails->customer_po_id=$stockUpdateDetails->customer_po_id;
                            $gatePassDetails->style_id=$stockUpdateDetails->style_id;
                            $gatePassDetails->item_id=$stockUpdateDetails->item_id;
                            $gatePassDetails->color_id=$stockUpdateDetails->color;
                            $gatePassDetails->store_id=$stockUpdateDetails->store;
                            $gatePassDetails->sub_store_id=$stockUpdateDetails->sub_store;
                            $gatePassDetails->bin_id=$stockUpdateDetails->bin;
                            $gatePassDetails->uom_id=$stockUpdateDetails->uom;
                            $gatePassDetails->material_code_id=$stockUpdateDetails->material_code;
                            $qty=$details[$i]["trns_qty"];
                            $gatePassDetails->trns_qty=$qty;
                            $gatePassDetails->save();
                            $stockTransaction->doc_num=$gate_pass_id;
                            $stockTransaction->doc_type="GATE_PASS";
                            $stockTransaction->style_id=$stockUpdateDetails->style_id;
                            $stockTransaction->size=$stockUpdateDetails->size;
                            $stockTransaction->customer_po_id=$stockUpdateDetails->customer_po_id;
                            $stockTransaction->item_id=$stockUpdateDetails->item_id;
                            $stockTransaction->color=$stockUpdateDetails->color;
                            $stockTransaction->main_store=$stockUpdateDetails->store;
                            $stockTransaction->sub_store=$stockUpdateDetails->sub_store;
                            $stockTransaction->bin=$stockUpdateDetails->bin;
                            $stockTransaction->uom=$stockUpdateDetails->uom;
                            $stockTransaction->material_code=$stockUpdateDetails->material_code;
                            $stockTransaction->location=$transer_location;
                            $stockTransaction->status="PLANED";
                            $stockTransaction->qty= -$qty;
                            $stockTransaction->save();
                          }
                            }


                           return response(['data'=>[
                           'message'=>'Items Transferd Successfully',

                         ]

                          ]

                     );




                    }



}
