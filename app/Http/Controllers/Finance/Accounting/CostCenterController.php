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

    public function get_list(Request $request)
    {
      $data = $request->all();
  		$start = $data['start'];
  		$length = $data['length'];
  		$draw = $data['draw'];
  		$search = $data['search']['value'];
  		$order = $data['order'][0];
  		$order_column = $data['columns'][$order['column']]['data'];
  		$order_type = $order['dir'];

      $cost_center_list = CostCenter::select('*')
  		->where('cost_center_code','like',$search.'%')
      ->orWhere('cost_center_name','like',$search.'%')
      ->orderBy($order_column, $order_type)
  		->offset($start)->limit($length)->get();

      $cost_center_count = CostCenter::select('*')
  		->where('cost_center_code','like',$search.'%')
      ->orWhere('cost_center_name','like',$search.'%')->count();

      echo json_encode(array(
  				"draw" => $draw,
  				"recordsTotal" => $cost_center_count,
  				"recordsFiltered" => $cost_center_count,
  				"data" => $cost_center_list
  		));
    }


    public function get(Request $request){
        $cost_center_id = $request->cost_center_id;
        $cost_center = CostCenter::find($cost_center_id);
        echo json_encode($cost_center);
    }


    public function check_code(Request $request)
  	{
  		$cost_center = CostCenter::where('cost_center_code','=',$request->cost_center_code)->first();
  		if($cost_center == null){
  			echo json_encode(array('status' => 'success'));
  		}
  		else if($cost_center->cost_center_id == $request->cost_center_id){
  			echo json_encode(array('status' => 'success'));
  		}
  		else {
  			echo json_encode(array('status' => 'error','message' => 'Cost center code already exists'));
  		}
  	}

    public function change_status(Request $request){
      $cost_center = CostCenter::find($request->cost_center_id);
      $cost_center->status = $request->status;
      $result = $cost_center->saveOrFail();
      echo json_encode(array('status' => 'success'));
    }


    public function get_active_list(Request $request)
    {
  		$search_c = $request->search;
  		$type_of_list = CostCenter::select('cost_center_id','cost_center_name')
  		->where([['cost_center_name', 'like', '%' . $search_c . '%'],]) ->get();
  		return response()->json($type_of_list);
  	}

}

?>
