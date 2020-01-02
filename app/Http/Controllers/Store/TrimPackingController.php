<?php

namespace App\Http\Controllers\Store;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Libraries\AppAuthorize;
use App\Libraries\CapitalizeAllFields;
use App\Models\Store\TrimPacking;
class TrimPackingController extends Controller{


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
      $trimPacking=new TrimPacking();
      if($trimPacking->validate($request->all()))
        {

          for($i=0;$i<count($request->dataset);$i++){
            $trimPacking=new TrimPacking();
            $data=$request->dataset[$i];
            $data=(object)$data;
            $binID=DB::table('org_store_bin')->where('store_bin_name','=',$data->bin)->select('store_bin_id')->first();

            $trimPacking->lot_no=$data->lot_no;
            $trimPacking->batch_no=$data->batch_no;
            $trimPacking->box_no=$data->box_no;
            $trimPacking->received_qty=$data->received_qty;
            $trimPacking->bin=$binID->store_bin_id;
            $trimPacking->shade=$data->shade;
            $trimPacking->comment=$data->comment;
            $trimPacking->invoice_no=$request->invoiceNo;
            $trimPacking->grn_detail_id=$request->grn_detail_id;
            $trimPacking->status=1;
            $trimPacking->save();


          }

          return response([ 'data' => [
            'message' => 'Trim Packing Detail Saved successfully',
            'trimPacking' => $trimPacking
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
