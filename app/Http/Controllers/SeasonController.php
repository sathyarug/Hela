<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Season;

class SeasonController extends Controller {

    public function index() {
        return view('season.season');
    }

    public function loadData() {
        $season_list = Season::all();
        echo json_encode($season_list);
    }

    public function checkCode(Request $request) {
        $count = Season::where('season_code', '=', $request->code)->count();

        if ($request->idcode > 0) {

            $user = Season::where('season_id', $request->idcode)->first();

            if ($user->season_code == $request->code) {
                $msg = true;
            } else {

                $msg = 'Already exists. please try another one';
            }
        } else {

            if ($count == 1) {

                $msg = 'Already exists. please try another one';
            } else {

                $msg = true;
            }
        }
        echo json_encode($msg);
    }

    public function saveSeason(Request $request) {
        $season = new Season();
        if ($season->validate($request->all())) {
            if ($request->season_hid > 0) {
                $season = Season::find($request->season_hid);
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
        echo json_encode(array('delete'));
    }

}
