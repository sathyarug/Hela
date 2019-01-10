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


 }
