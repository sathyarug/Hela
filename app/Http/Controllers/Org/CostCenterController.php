<?php

namespace App\Http\Controllers\Org;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Org\CostCenter;

class CostCenterController extends Controller
{
    public function new(){       
        return view('org.cost_center.cost_center');
    }
    
    public function save(Request $request){         
        $cost_center = new CostCenter();       
        if ($cost_center->validate($request->all()))   
        {
            if($cost_center->cost_center_id > 0){
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
    
}

?>
