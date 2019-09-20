<?php

namespace App\Http\Controllers\Store;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\stores\StoRollDescription;
use App\Models\stores\PoOrderDetails;
use App\Models\store\StoRollFabricinSpection;
use App\Models\Stores\RollPlan;
use App\Models\Store\GrnHeader;
use App\Models\Store\GrnDetail;
use APP\Models\Stores\fabricInspection;
use DB;

class FabricInspectionController extends Controller
{
  public function index(Request $request)
  {
    $type = $request->type;
    if($type == 'datatable')   {
      $data = $request->all();
      return response($this->datatable_search($data));
    }
    else if($type == 'autoInvoice')    {
      $search = $request->search;
      return response($this->autocomplete_search_invoice($search));
    }

    else if($type == 'autoBatchNO')    {
      $search = $request->search;
      return response($this->autocomplete_search_batchNo($search));
    }
    else if($type=='autoStatusTypes'){
      $search = $request->search;
      return response($this->autocomplete_search_inspection_status($search));
    }
    else {
      $active = $request->active;
      $fields = $request->fields;
      return response([
        'data' => $this->list($active , $fields)
      ]);
    }
  }


  public function store(Request $request)
  {

      $fabricinSpection = new fabricInspection();
      if($fabricinSpection->validate($request->all()))
      {
        $category->fill($request->all());
        $category->status = 1;
        //$capitalizeAllFields=CapitalizeAllFields::setCapitalAll($category);
        $category->save();

        return response([ 'data' => [
          'message' => 'Cancellation category saved successfully',
          'cancellationCategory' => $category
          ]
        ], Response::HTTP_CREATED );
      }
      else
      {
          $errors = $category->errors();// failure, get errors
          $errors_str = $category->errors_tostring();
          return response(['errors' => ['validationErrors' => $errors, 'validationErrorsText' => $errors_str]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }

  }

  private function autocomplete_search_invoice($search){

    $invoice_list = GrnHeader::select('inv_number')->distinct('inv_number')
    ->where([['inv_number', 'like', '%' . $search . '%'],]) ->get();
    return $invoice_list;



  }
  private function autocomplete_search_batchNo($search){
    $invoice_list = GrnHeader::select('batch_no')->distinct('batch_no')
    ->where([['batch_no', 'like', '%' . $search . '%'],]) ->get();
    return $invoice_list;

  }
  private function autocomplete_search_inspection_status($search){
    //dd($search);
    $inspectionStatus=DB::table('store_inspec_status')->where('status_name','like','%'.$search.'%')->pluck('status_name');
    return $inspectionStatus;

  }
    public function store(Request $request)
    {


        foreach ($request->all()['roll_info'] as $key => $value)
        {
            $fabricinSpection= new StoRollFabricinSpection();

//            print_r($value);exit;
//            $fabricinSpection->item_code=$value['roll_no'];
            $fabricinSpection->roll_no=$value['roll_no'];
            $fabricinSpection->purchase_weight=$value['purchase_weight'];
            $fabricinSpection->save();

        }exit;

    }

    public function search_rollPlan_details(Request $request){
      $batch_no=$request->batchNo;
      $invoice_no=$request->invoiceNo;
      //dd($batch_no);
      $rollPlanDetails=DB::SELECT("SELECT store_roll_plan.*
          From store_roll_plan
          INNER JOIN store_grn_detail on store_roll_plan.grn_detail_id=store_grn_detail.grn_detail_id
          INNER JOIN store_grn_header on store_grn_detail.grn_id=store_grn_header.grn_id
          WHERE store_roll_plan.invoice_no='".$invoice_no."'
          AND store_roll_plan.batch_no='".$batch_no."'");

          return response([
              'data' => $rollPlanDetails
          ]);
    }

}
