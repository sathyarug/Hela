<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Location;
use App;
use App\Models\Org\CostCenter;
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
            echo 'Saved';
        }else{
           $errors = $profile->errors_tostring();
           return $errors;
        }
    }

    public function validateEmpNo(Request $request){
        $user = User::where('user_name',Input::get('user_name'))->first();
        if(is_null($user)){
            echo json_encode(true);
        }else{
            echo json_encode(false);

        }
    }
    

}
