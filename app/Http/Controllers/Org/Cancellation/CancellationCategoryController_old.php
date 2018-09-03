<?php

namespace App\Http\Controllers\Org\Cancellation;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Org\Cancellation\CancellationCategory;

class CancellationCategoryController extends Controller
{
   public function index() {
        return view('cancellation.cancellation_category');
    }

    public function loadData() {
        $category_list = CancellationCategory::all();
        echo json_encode($category_list);
    }

    public function checkCode(Request $request) {
        $count = CancellationCategory::where('category_code', '=', $request->code)->count();

        if ($request->idcode > 0) {

            $user = CancellationCategory::where('category_id', $request->idcode)->first();

            if ($user->category_code == $request->code) {
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

    public function saveCategory(Request $request) {
        $category = new CancellationCategory();
        if ($category->validate($request->all())) {
            if ($request->category_hid > 0) {
                $category = CancellationCategory::find($request->category_hid);
                $category->category_description=$request->category_description;
            } else {
                $category->fill($request->all());
                $category->status = 1;
                $category->created_by = 1;
            }
            $category = $category->saveOrFail();
            // echo json_encode(array('Saved'));
            echo json_encode(array('status' => 'success', 'message' => 'Category details saved successfully.'));
        } else {
            // failure, get errors
            $errors = $category->errors();
            echo json_encode(array('status' => 'error', 'message' => $errors));
        }
    }

    public function edit(Request $request) {
        $category_id = $request->category_id;
        $category = CancellationCategory::find($category_id);
        echo json_encode($category);
    }

    public function delete(Request $request) {
        $category_id = $request->category_id;
        //$source = Main_Source::find($source_id);
        //$source->delete();
        $category = CancellationCategory::where('category_id', $category_id)->update(['status' => 0]);
        echo json_encode(array('delete'));
    }
}
