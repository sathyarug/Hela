<?php

namespace App\Http\Controllers\Org ;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Org\Color;

class ColorController extends Controller
{
    public function new(){

        return view('org.color.color');
    }

    public function save(Request $request){
        $color = new Color();
        if ($color->validate($request->all()))
        {
            if($request->color_id > 0){
                $color = Color::find($request->color_id);
            }
            else{
              $color->fill($request->all());
              $color->created_by = 1;
              $color->status = 1;
            }
            $result = $color->saveOrFail();
            echo json_encode(array('status' => 'success' , 'message' => 'Color details saved successfully.'));
        }
        else
        {
            // failure, get errors
            $errors = $color->errors_tostring();
            echo json_encode(array('status' => 'error' , 'message' => $errors));
        }

    }

    public function get_list(){
        $color_list = Color::all();
        echo json_encode($color_list);
    }


    public function get(Request $request){
        $color_id = $request->color_id;
        $color = Color::find($color_id);
        echo json_encode($color);
    }

    public function check_color_code(Request $request)
    {
        $count = Color::where('color_code','=',$request->color_code)->count();
        if($count >= 1){
              $msg = 'Color code already exists';
          }else{
              $msg = true;
        }
        echo json_encode($msg);
    }

    public function check_color_name(Request $request)
    {
        $count = Color::where('color_name','=',$request->color_name)->count();
        if($count >= 1){
              $msg = 'Color name already exists';
          }else{
              $msg = true;
        }
        echo json_encode($msg);
    }

    public function change_status(Request $request){
      $color = Color::find($request->color_id);
      $color->status = $request->status;
      $result = $color->saveOrFail();
      echo json_encode(array('status' => 'success'));
    }

}

?>
