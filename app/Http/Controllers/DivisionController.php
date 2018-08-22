<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Division;

class DivisionController extends Controller {

    public function index() {
        return view('division.division');
    }

    //check duplicate
    public function loadData(Request $request) {
      $data = $request->all();
      $start = $data['start'];
      $length = $data['length'];
      $draw = $data['draw'];
      $search = $data['search']['value'];
      $order = $data['order'][0];
      $order_column = $data['columns'][$order['column']]['data'];
      $order_type = $order['dir'];

      $division_list = Division::select('*')
      ->where('division_code'  , 'like', $search.'%' )
      ->orWhere('division_description'  , 'like', $search.'%' )
      ->orderBy($order_column, $order_type)
      ->offset($start)->limit($length)->get();

      $division_count = Division::where('division_code'  , 'like', $search.'%' )
      ->orWhere('division_description'  , 'like', $search.'%' )
      ->count();

      echo json_encode(array(
          "draw" => $draw,
          "recordsTotal" => $division_count,
          "recordsFiltered" => $division_count,
          "data" => $division_list
      ));
    }


    public function check_code(Request $request)
    {
      $division = Division::where('division_code','=',$request->division_code)->first();
      if($division == null){
        echo json_encode(array('status' => 'success'));
      }
      else if($division->division_id == $request->division_id){
        echo json_encode(array('status' => 'success'));
      }
      else {
        echo json_encode(array('status' => 'error','message' => 'Division code already exists'));
      }
    }


    public function saveDivision(Request $request) {
        $division = new Division();
        if ($division->validate($request->all())) {
            if ($request->division_id > 0) {
                $division = Division::find($request->division_id);
                $division->division_description = $request->division_description;
            } else {
                $division->fill($request->all());
                $division->status = 1;
                $division->created_by = 1;
            }

            $division = $division->saveOrFail();
            // echo json_encode(array('Saved'));
            echo json_encode(array('status' => 'success', 'message' => 'Source details saved successfully.'));
        } else {
            // failure, get errors
            $errors = $division->errors();
            echo json_encode(array('status' => 'error', 'message' => $errors));
        }
    }

    public function edit(Request $request) {
        $division_id = $request->division_id;
        $division = Division::find($division_id);
        echo json_encode($division);
    }

    public function delete(Request $request) {
        $division_id = $request->division_id;
        //$source = Main_Source::find($source_id);
        //$source->delete();
        $division = Division::where('division_id', $division_id)->update(['status' => 0]);
        echo json_encode(array(
          'status' => 'success',
          'message' => 'Division was deactivated successfully.'
        ));
    }

}
