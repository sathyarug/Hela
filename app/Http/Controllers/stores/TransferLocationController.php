<?php
namespace App\Http\Controllers\Stores;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use App\Models\Merchandising\CustomerOrder;
use App\Models\Merchandising\CustomerOrderDetails;
use App\Models\Merchandising\StyleCreation;
use App\Models\Finance\Item\SubCategory;
use App\Models\Merchandising\Item\Item;
use App\Models\stores\RollPlan;
use App\Models\Store\Stock;
use App\Models\stores\TransferLocationUpdate;
use App\models\stores\GatePassHeader;
use App\models\stores\GatePassDetails;
use App\models\store\StockTransaction;
use App\Models\Store\GrnHeader;
use App\Models\Merchandising\ShopOrderHeader;
use App\Models\Merchandising\ShopOrderDetail;
use App\Models\Store\GrnDetail;
use Illuminate\Support\Facades\DB;

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

   $stylefrom=ShopOrderHeader::join('merc_shop_order_detail','merc_shop_order_header.shop_order_id','=','merc_shop_order_detail.shop_order_id')
                            ->join('bom_header','merc_shop_order_detail.bom_id','=','bom_header.bom_id')
                           ->join('costing','merc_shop_order_detail.costing_id','=','costing.id')
                          ->join('style_creation','costing.style_id','=','style_creation.style_id')
                          ->select('style_creation.style_no')
                          ->where('merc_shop_order_detail.shop_order_id','=',$searchFrom)
                          ->where('style_creation.status','=',1)
                          ->first();
  $styleTo=ShopOrderHeader::join('merc_shop_order_detail','merc_shop_order_header.shop_order_id','=','merc_shop_order_detail.shop_order_id')
                          ->join('bom_header','merc_shop_order_detail.bom_id','=','bom_header.bom_id')
                          ->join('costing','merc_shop_order_detail.costing_id','=','costing.id')
                          ->join('style_creation','costing.style_id','=','style_creation.style_id')
                         ->select('style_creation.style_no')
                         ->where('merc_shop_order_detail.shop_order_id','=',$searchTo)
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

                        //dd($style);



                    /*    $details=Stock::join('style_creation','store_stock.style_id','=','style_creation.style_id')
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
                                        */


                                        $detailsTrimPacking=GrnHeader::join('store_grn_detail','store_grn_header.grn_id','=','store_grn_detail.grn_id')
                                                       ->Join('style_creation','store_grn_detail.style_id','=','style_creation.style_id')
                                                      ->Join('store_trim_packing_detail','store_grn_detail.grn_detail_id','=','store_trim_packing_detail.grn_detail_id')
                                                      ->join('item_master','store_grn_detail.item_code','=','item_master.master_id')
                                                      ->join('org_store_bin','store_trim_packing_detail.bin','=','org_store_bin.store_bin_id')
                                                      ->select('store_trim_packing_detail.*','item_master.master_code','org_store_bin.store_bin_name','store_grn_header.main_store','store_grn_header.sub_store','store_grn_detail.style_id','store_grn_detail.size','store_grn_detail.uom','store_grn_detail.shop_order_id','store_grn_detail.shop_order_detail_id')
                                                      ->where('style_creation.style_no','=',$style)
                                                      ->where('store_trim_packing_detail.user_loc_id','=',$user_location);

                                                  $detailsRollPlan=GrnHeader::join('store_grn_detail','store_grn_header.grn_id','=','store_grn_detail.grn_id')
                                                                ->join('style_creation','store_grn_detail.style_id','=','style_creation.style_id')
                                                                ->join('store_roll_plan','store_grn_detail.grn_detail_id','=','store_roll_plan.grn_detail_id')
                                                                ->join('item_master','store_grn_detail.item_code','=','item_master.master_id')
                                                                ->join('org_store_bin','store_roll_plan.bin','=','org_store_bin.store_bin_id')
                                                                ->select('store_roll_plan.*','item_master.master_code','org_store_bin.store_bin_name','store_grn_header.main_store','store_grn_header.sub_store','store_grn_detail.style_id','store_grn_detail.size','store_grn_detail.color','store_grn_detail.uom',,'store_grn_detail.shop_order_id','store_grn_detail.shop_order_detail_id')
                                                                ->where('style_creation.style_no','=',$style)
                                                                ->where('store_roll_plan.user_loc_id','=',$user_location)
                                                              //  ->get();
                                                              ->union($detailsTrimPacking)
                                                              ->get();

                                                        return $detailsRollPlan;

                      }

                      private function setStatuszero($details){
                        for($i=0;$i<count($details);$i++){
                          $id=$details[$i]["id"];
                          //$setStatusZero=TransferLocationUpdate::find($id);
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

                              $stockUpdateDetails= TransferLocationUpdate::select('*')
                              ->where('style_id','=',$details[$i]['style_id'])
                              ->where('item_id','=',$details[$i]['item_id'])
                              ->where('store','=',$details[$i]['main_store'])
                              ->where('sub_store','=',$details[$i]['sub_store_id'])
                              ->where('bin','=',$details[$i]['bin'])
                              ->where('location','=',$transer_location)
                              ->first();

                              $itemType=Item::join('item_category','item_master.category_id','=','item_category.category_id')
                                             ->select('item_category.category_code')
                                             ->where('item_master.master_id','=',$details[$i]['item_code'])
                                             ->first();
                                               if($itemType->category_code=="FA"){
                                                 $rollPlan=RollPlan::find($details[$i]['roll_plan_id']);
                                                 $rollPlan->qty=$rollPlan->qty-$details[$i]['trans_qty'];
                                                 $rollPlan->save();

                                               }
                                                 else if($itemType->category_code=!"FA") {
                                                  $trimPacking=TrimPacking::find($details[$i]['roll_plan_id']);
                                                  $trimPacking->qty=$trimPacking->qty-$details[$i]['trans_qty'];
                                                  $trimPacking->save();

                                                 }




                            $stockUpdateDetails->total_qty=$stockUpdateDetails->$details[$i]['trans_qty'];
                           //$stockUpdateDetails->status=1;
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
                            //$id=$details[$i]["id"];
                            $itemType=Item::join('item_category','item_master.category_id','=','item_category.category_id')
                                           ->select('item_category.category_code')
                                           ->where('item_master.master_id','=',$details[$i]['item_code'])
                                           ->first();
                            $gatePassDetails= new GatePassDetails();
                            $stockTransaction=new StockTransaction();
                            //if($stockUpdateDetails->transfer_status=="transfer"){
                            $gatePassDetails->gate_pass_id=$gate_pass_id;
                            $gatePassDetails->size_id=$details[$i]['size'];
                            $gatePassDetails->shop_order_id=$details[$i]['shop_order_id'];
                            $gatePassDetails->shop_order_detail_id=$details[$i]['shop_order_detail_id'];
                            $gatePassDetails->style_id=$details[$i]['style_id'];
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
                          //}
                            }


                           return response(['data'=>[
                           'message'=>'Items Transferd Successfully',

                         ]

                          ]

                     );




                    }



}
