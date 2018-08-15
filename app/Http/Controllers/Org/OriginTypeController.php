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

      $origin_type_list = OriginType::select('*')
      ->where('origin_type'  , 'like', $search.'%' )
      ->orderBy($order_column, $order_type)
      ->offset($start)->limit($length)->get();

      $origin_type_count = OriginType::where('origin_type'  , 'like', $search.'%' )->count();

      echo json_encode(array(
          "draw" => $draw,
          "recordsTotal" => $origin_type_count,
          "recordsFiltered" => $origin_type_count,
          "data" => $origin_type_list
      ));
    }


    public function get(Request $request){
        $origin_type_id = $request->origin_type_id;
        $origin_type = OriginType::find($origin_type_id);
        echo json_encode($origin_type);
    }

    public function check_code(Request $request)
    {
      $origin_type = OriginType::where('origin_type','=',$request->origin_type)->first();
      if($origin_type == null){
        echo json_encode(array('status' => 'success'));
      }
      else if($origin_type->origin_type_id == $request->origin_type_id){
        echo json_encode(array('status' => 'success'));
      }
      else {
        echo json_encode(array('status' => 'error','message' => 'Origin type already exists'));
      }
    }

    public function change_status(Request $request){
      $origin_type = OriginType::find($request->origin_type_id);
      $origin_type->status = $request->status;
      $result = $origin_type->saveOrFail();
      echo json_encode(array(
        'status' => 'success',
        'message' => 'Origin type was deactivated successfully.'
      ));
    }

}

?>
