<?php

namespace App\Http\Controllers\Finance\Accounting;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Finance\Accounting\CostCenter;

class CostCenterController extends Controller
{
    public function new(){
        return view('org.cost_center.cost_center');
    }

    public function save(Request $request){
        $cost_center = new CostCenter();
        if ($cost_center->validate($request->all()))
        {
            if($request->cost_center_id > 0){
                $cost_center = CostCenter::find($request->cost_center_id);
            }
            $cost_center->fill($request->all());
            $cost_center->created_by = 1;
            $result = $cost_center->saveOrFail();
            echo json_encode(array('status' => 'success' , 'message' => 'Cost center details saved successfully.'));
        }
        else
        {
            // failure, get errors
            $errors = $cost_center->errors_tostring();
            echo json_encode(array('status' => 'error' , 'message' => $errors));
        }

    }

    public function get_list(){
        $cost_center_list = CostCenter::all();
        echo json_encode($cost_center_list);
    }


    public function get(Request $request){
        $cost_center_id = $request->cost_center_id;
        $cost_center = CostCenter::find($cost_center_id);
        echo json_encode($cost_center);
    }

    public function check_cost_center_code(Request $request)
    {
        $count = CostCenter::where('cost_center_code','=',$request->cost_center_code)->count();
        if($count >= 1){
              $msg = 'Cost center code already exists';
          }else{
              $msg = true;
        }
        echo json_encode($msg);
    }

    public function change_status(Request $request){
      $cost_center = CostCenter::find($request->cost_center_id);
      $cost_center->status = $request->status;
      $result = $cost_center->saveOrFail();
      echo json_encode(array('status' => 'success'));
    }

}

?>
