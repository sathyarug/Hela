<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Season;

class SeasonController extends Controller {

    public function index() {
        return view('season.season');
    }

    public function loadData(Request $request) {
        $data = $request->all();
        $start = $data['start'];
        $length = $data['length'];
        $draw = $data['draw'];
        $search = $data['search']['value'];
        $order = $data['order'][0];
        $order_column = $data['columns'][$order['column']]['data'];
        $order_type = $order['dir'];

        $season_list = Season::select('*')
        ->where('season_code'  , 'like', $search.'%' )
        ->orWhere('season_name'  , 'like', $search.'%' )
        ->orderBy($order_column, $order_type)
        ->offset($start)->limit($length)->get();

        $season_count = Season::where('season_code'  , 'like', $search.'%' )
        ->orWhere('season_name'  , 'like', $search.'%' )
        ->count();

        echo json_encode(array(
            "draw" => $draw,
            "recordsTotal" => $season_count,
            "recordsFiltered" => $season_count,
            "data" => $season_list
        ));
    }


    public function check_code(Request $request)
    {
      $season = Season::where('season_code','=',$request->season_code)->first();
      if($season == null){
        echo json_encode(array('status' => 'success'));
      }
      else if($season->season_id == $request->season_id){
        echo json_encode(array('status' => 'success'));
      }
      else {
        echo json_encode(array('status' => 'error','message' => 'Season code already exists'));
      }
    }


    public function saveSeason(Request $request) {
        $season = new Season();
        if ($season->validate($request->all())) {
            if ($request->season_id > 0) {
                $season = Season::find($request->season_id);
                $season->season_name = $request->season_name;
            } else {
                $season->fill($request->all());
                $season->status = 1;
                $season->created_by = 1;
            }

            $season = $season->saveOrFail();
            // echo json_encode(array('Saved'));
            echo json_encode(array('status' => 'success', 'message' => 'Season details saved successfully.'));
        } else {
            // failure, get errors
            $errors = $season->errors();
            echo json_encode(array('status' => 'error', 'message' => $errors));
        }
    }

    public function edit(Request $request) {
        $season_id = $request->season_id;
        $season = Season::find($season_id);
        echo json_encode($season);
    }

    public function delete(Request $request) {
        $season_id = $request->season_id;
        //$source = Main_Source::find($source_id);
        //$source->delete();
        $season = Season::where('season_id', $season_id)->update(['status' => 0]);
        echo json_encode(array(
          'status' => 'success',
          'message' => 'Season was deactivated successfully.'
        ));
    }

}
