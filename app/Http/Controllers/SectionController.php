<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Section;

class SectionController extends Controller {

    public function index() {
        return view('section.section');
    }

    public function loadData(Request $request)
    {
      $data = $request->all();
      $start = $data['start'];
      $length = $data['length'];
      $draw = $data['draw'];
      $search = $data['search']['value'];
      $order = $data['order'][0];
      $order_column = $data['columns'][$order['column']]['data'];
      $order_type = $order['dir'];

      $section_list = Section::select('*')
      ->where('section_code'  , 'like', $search.'%' )
      ->orWhere('section_name'  , 'like', $search.'%' )
      ->orderBy($order_column, $order_type)
      ->offset($start)->limit($length)->get();

      $section_count = Section::where('section_code'  , 'like', $search.'%' )
      ->orWhere('section_name'  , 'like', $search.'%' )
      ->count();

      echo json_encode(array(
          "draw" => $draw,
          "recordsTotal" => $section_count,
          "recordsFiltered" => $section_count,
          "data" => $section_list
      ));
    }


    public function check_code(Request $request)
    {
      $section = Section::where('section_code','=',$request->section_code)->first();
      if($section == null){
        echo json_encode(array('status' => 'success'));
      }
      else if($section->section_id == $request->section_id){
        echo json_encode(array('status' => 'success'));
      }
      else {
        echo json_encode(array('status' => 'error','message' => 'Section code already exists'));
      }
    }


    public function saveSection(Request $request) {
        $section = new Section();
        if ($section->validate($request->all())) {
            if ($request->section_id > 0) {
                $section = Section::find($request->section_id);
                $section->section_name=$request->section_name;
            } else {
                $section->fill($request->all());
                $section->status = 1;
                $section->created_by = 1;
            }
            $section = $section->saveOrFail();
            // echo json_encode(array('Saved'));
            echo json_encode(array('status' => 'success', 'message' => 'Section details saved successfully.'));
        } else {
            // failure, get errors
            $errors = $section->errors();
            echo json_encode(array('status' => 'error', 'message' => $errors));
        }
    }

    public function edit(Request $request) {
        $section_id = $request->section_id;
        $section = Section::find($section_id);
        echo json_encode($section);
    }

    public function delete(Request $request) {
        $section_id = $request->section_id;
        //$source = Main_Source::find($source_id);
        //$source->delete();
        $section = Section::where('section_id', $section_id)->update(['status' => 0]);
        echo json_encode(array(
          'status' => 'success',
          'message' => 'Division was deactivated successfully.'
        ));
    }

}
