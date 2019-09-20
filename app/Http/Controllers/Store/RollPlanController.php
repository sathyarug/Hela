<?php

namespace App\Http\Controllers\Store;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Controller;
use App\Models\stores\PoOrderDetails;
use App\Models\stores\PoOrderHeader;
use App\Models\stores\PoOrderType;
use App\Models\stores\RollPlan;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Libraries\AppAuthorize;
use App\Libraries\CapitalizeAllFields;
class RollPlanController extends Controller
{

  var $authorize = null;

  public function __construct()
  {
    //add functions names to 'except' paramert to skip authentication
    $this->middleware('jwt.verify', ['except' => ['index']]);
    $this->authorize = new AppAuthorize();
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
    else {
    $this->store($request);
    }
  }
//create roll plan
  public function store(Request $request)
  {
      $rollPlan = new RollPlan();
      //dd($request->invoiceNo);
    if($rollPlan->validate($request->all()))
      {
        for($i=0;$i<count($request->dataset);$i++)
        {
        $rollPlan = new RollPlan();
        $data=$request->dataset[$i];
        $data=(object)$data;
        $binID=DB::table('org_store_bin')->where('store_bin_name','=',$data->bin)->select('store_bin_id')->first();
        //dd();
        $rollPlan->lot_no=$data->lot_no;
        $rollPlan->batch_no=$data->batch_no;
        $rollPlan->roll_no=$data->roll_no;
        $rollPlan->qty=$data->qty;
        $rollPlan->received_qty=$data->received_qty;
        $rollPlan->bin=$binID->store_bin_id;
        ///dd($binID);
        $rollPlan->width=$data->width;
        $rollPlan->shade=$data->shade;
        $rollPlan->comment=$data->comment;
        //$rollPlan->barcode=$data->barcode;
        $rollPlan->invoice_no=$request->invoiceNo;
        $rollPlan->grn_detail_id=$request->grn_detail_id;
        $rollPlan->status = 1;
        $rollPlan->save();
        }
        return response([ 'data' => [
          'message' => 'Roll Plan saved successfully',
          'rollPlan' => $rollPlan
          ]
        ], Response::HTTP_CREATED );
     }
      else
      {
          $errors = $store->errors();// failure, get errors
          return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }


  }







}
