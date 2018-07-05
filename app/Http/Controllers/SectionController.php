<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Section;

class SectionController extends Controller {

    public function index() {
        return view('section.section');
    }

    public function loadData() {
        $section_list = Section::all();
        echo json_encode($section_list);
    }

    public function checkCode(Request $request) {
        $count = Section::where('section_code', '=', $request->code)->count();

        if ($request->idcode > 0) {

            $user = Section::where('section_id', $request->idcode)->first();

            if ($user->section_code == $request->code) {
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

    public function saveSection(Request $request) {
        $section = new Section();
        if ($section->validate($request->all())) {
            if ($request->section_hid > 0) {
                $section = Section::find($request->section_hid);
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
        echo json_encode(array('delete'));
    }

}
