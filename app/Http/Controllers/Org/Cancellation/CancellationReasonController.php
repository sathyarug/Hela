<?php

namespace App\Http\Controllers\Org\Cancellation;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Org\Cancellation\CancellationReason;

class CancellationReasonController extends Controller
{
 public function index() {
        return view('cancellation.cancellation_reason');
    }

    public function loadData() {
        $reason_list = CancellationReason::all();
        echo json_encode($reason_list);
    }

    public function checkCode(Request $request) {
        $count = CancellationReason::where('reason_code', '=', $request->code)->count();

        if ($request->idcode > 0) {

            $user = CancellationReason::where('reason_id', $request->idcode)->first();

            if ($user->reason_code == $request->code) {
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

    public function saveReason(Request $request) {
        $reason = new CancellationReason();
        if ($reason->validate($request->all())) {
            if ($request->reason_hid > 0) {
                $reason = CancellationReason::find($request->reason_hid);
                $reason->reason_description=$request->reason_description;
            } else {
                $reason->fill($request->all());
                $reason->status = 1;
                $reason->created_by = 1;
            }
            $reason = $reason->saveOrFail();
            // echo json_encode(array('Saved'));
            echo json_encode(array('status' => 'success', 'message' => 'Reason details saved successfully.'));
        } else {
            // failure, get errors
            $errors = $reason->errors();
            echo json_encode(array('status' => 'error', 'message' => $errors));
        }
    }

    public function edit(Request $request) {
        $reason_id = $request->reason_id;
        $reason = CancellationReason::find($reason_id);
        echo json_encode($reason);
    }

    public function delete(Request $request) {
        $reason_id = $request->reason_id;
        //$source = Main_Source::find($source_id);
        //$source->delete();
        $reason = CancellationReason::where('reason_id', $reason_id)->update(['status' => 0]);
        echo json_encode(array('delete'));
    }
}
