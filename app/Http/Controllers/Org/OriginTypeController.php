<?php

namespace App\Http\Controllers\Org ;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Org\OriginType;

class OriginTypeController extends Controller
{
    public function new(){

        return view('org.origin_type.origin_type');
    }

    public function save(Request $request){
        $origin_type = new OriginType();
        if ($origin_type->validate($request->all()))
        {
            if($request->origin_type_id > 0){
                $origin_type = OriginType::find($request->origin_type_id);
            }
            else{
              $origin_type->fill($request->all());
              $origin_type->created_by = 1;
              $origin_type->status = 1;
            }
            $result = $origin_type->saveOrFail();
            echo json_encode(array('status' => 'success' , 'message' => 'Origin type details saved successfully.'));
        }
        else
        {
            // failure, get errors
            $errors = $origin_type->errors_tostring();
            echo json_encode(array('status' => 'error' , 'message' => $errors));
        }

    }

    public function get_list(){
        $origin_type_list = OriginType::all();
        echo json_encode($origin_type_list);
    }


    public function get(Request $request){
        $origin_type_id = $request->origin_type_id;
        $origin_type = OriginType::find($origin_type_id);
        echo json_encode($origin_type);
    }

    public function check_origin_type(Request $request)
    {
        $count = OriginType::where('origin_type','=',$request->origin_type)->count();
        if($count >= 1){
              $msg = 'Origin type already exists';
          }else{
              $msg = true;
        }
        echo json_encode($msg);
    }

    public function change_status(Request $request){
      $origin_type = OriginType::find($request->origin_type_id);
      $origin_type->status = $request->status;
      $result = $origin_type->saveOrFail();
      echo json_encode(array('status' => 'success'));
    }

}

?>
