<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Location;
use App;
use App\Models\Finance\Accounting\CostCenter;
use App\Models\Admin\UsrProfile;
use App\Models\Admin\User;
use App\Models\Admin\UsrDepartment;
use App\Models\Admin\UsrDesignation;

class UserController extends Controller {

    public function register(){
        $location = App\OrgLocation::pluck('loc_name', 'loc_id')->toArray();
        $dept = UsrDepartment::pluck('dept_name', 'dept_id')->toArray();
        $costCtr = CostCenter::pluck('cost_center_code', 'cost_center_id')->toArray();
        $desg = UsrDesignation::pluck('desig_name', 'desig_id')->toArray();

        $data = [
            'location' => $location,
            'dept' => $dept,
            'desg' => $desg,
            'costCtr' => $costCtr
        ];

        return view('/user/register')->with('data', $data);
    }

    public function user(){
        return view('/user/user');
    }

    public function store(Request $request)
    {

        $data = request()->except(['_token']);
        $profile = new UsrProfile;
        $login = new User;

        $profile->fill($request->all());

        $login->user_name = $request->user_name;
        $login->password = Hash::make($request->password);

        if ($profile->validate($request->all()) && $login->validate($request->all())) {
            $profile->save();

            //Adding user id user login table
            $login->user_id = $profile->user_id;
            $login->save();
            return redirect()->route('admin/user');
        }else{
           $errors = $profile->errors_tostring();
           return $errors;
        }
    }

    public function validateUserName(Request $request){
        $user = User::where('user_name',Input::get('user_name'))->first();
        if(is_null($user))
            echo json_encode(true);
        else
            echo json_encode(false);
    }

    public function validateEmpNo(Request $request){
        $emp = UsrProfile::where('emp_number',Input::get('emp_number'))->first();
        if(is_null($emp))
            echo json_encode(true);
        else
            echo json_encode(false);
    }

    public function loadReportLevels(Request $request){
         dd($request); exit;
        //echo response()->json($posts);
        $query = $request->get('q','');

        $posts = UsrProfile::where('first_name','LIKE','%'.$query.'%')->limit(5)->get();

        echo json_encode($posts);

    }

    public function getUserList() {
        //UsrProfile::all()->sortByDesc("created_at")->sortByDesc("status")

        return datatables()->of(DB::table('usr_profile as t1')
            ->select("t1.user_id", "t1.first_name", "t1.last_name", "t1.emp_number", "t1.email", "t2.dept_name", "t3.desig_name", "t4.loc_name" )
            ->join("usr_department AS t2", "t1.dept_id", "=", "t2.dept_id")
            ->join("usr_designation AS t3", "t1.desig_id", "=", "t3.desig_id")
            ->join("org_location AS t4", "t1.loc_id", "=", "t4.loc_id")
            ->get())->toJson();

        //return datatables()->query(DB::table('users'))->toJson();


    }
    

}
