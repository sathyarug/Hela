<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App;
use App\Models\Finance\Accounting\CostCenter;
use App\Models\Admin\UsrProfile;
use App\Models\Admin\User;
use App\Models\Admin\UsrDepartment;
use App\Models\Admin\UsrDesignation;
use App\Models\Admin\Role;
use App\Models\Org\Location\Location;

class UserController extends Controller {

  public function __construct()
  {
    //add functions names to 'except' paramert to skip authentication
    $this->middleware('jwt.verify', ['except' => ['index']]);
  }

  //get user list
  public function index(Request $request)
  {
    $type = $request->type;
    if($type == 'datatable')   {
      $data = $request->all();
      return response($this->datatable_search($data));
    }
    else if($type == 'auto')    {
      $search = $request->search;
      return response($this->autocomplete_search($search));
    }
    else if($type == 'auto_has_login')    {
      $search = $request->search;
      return response($this->autocomplete_search_has_login($search));
    }
    else {
      $active = $request->active;
      $fields = $request->fields;
      return response([
        'data' => $this->list($active , $fields)
      ]);
    }
  }

    /*public function register(){
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
    }*/

  /*  public function user(){
        return view('/user/user');
    }*/

    public function store(Request $request)
    {
    //  $data = request()->except(['_token']);
      $profile = new UsrProfile;
      $login = new User;

      if($profile->validate($request->all()))
      {
        $profile->fill($request->except(['username', 'password']));
        $profile->status = 1;
        $profile->save();

        if($profile->user_id > 0 && $request->user_name != null && $request->password != null){
          $login->user_id = $profile->user_id;
          $login->user_name = $request->user_name;
          $login->password = Hash::make($request->password);
          $login->save();
        }

        return response([ 'data' => [
          'message' => 'User profile saved successfully',
          'user_profile' => $profile,
          'user' => $login
          ]
        ], Response::HTTP_CREATED );
      }
      else
      {
          $errors = $color->errors();// failure, get errors
          return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }

    //deactivate a Designation
    public function destroy($id)
    {
        $user = UsrProfile::where('user_id', $id)->update(['status' => 0]);
        return response([
          'data' => [
            'message' => 'User deactivated successfully.',
            'department' => $user,
            'status'=>'1'
          ]
        ]);

        }

    //get a Company
    public function show($id)
    {
      $user = UsrProfile::join('org_designation','usr_profile.desig_id','=','org_designation.des_id')
      ->join('usr_login', 'usr_login.user_id', '=', 'usr_profile.user_id')
      ->select('usr_profile.*','org_designation.des_name','org_designation.des_id','usr_login.user_name','usr_login.password')
      ->where('usr_profile.user_id','=',$id)
      ->first();
      if($user == null)
        throw new ModelNotFoundException("Requested user not found", 1);
      else
        return response([ 'data' => $user ]);
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
            ->join("org_designation AS t3", "t1.desig_id", "=", "t3.des_id")
            ->join("org_location AS t4", "t1.loc_id", "=", "t4.loc_id")
            ->get())->toJson();

        //return datatables()->query(DB::table('users'))->toJson();


}
//update user profile
public function update(Request $request, $id)
{
    $user = UsrProfile::find($id);
      $user->fill($request->all());
      $user->save();

      return response([
        'data' => [
          'message' => 'User Updated Successfully',
          'country' => $user
        ]
      ]);


}


  public function roles(Request $request){
    $type = $request->type;
    $user_id = $request->user_id;
    $location = $request->location;

    if($type == 'assigned')
    {
      $assigned = Role::select('role_id','role_name')
      ->whereIn('role_id' , function($selected) use ($user_id, $location){
          $selected->select('role_id')
          ->from('user_roles')
          ->where('user_id', $user_id)
          ->where('loc_id', $location);
      })->get();
      return response([ 'data' => $assigned]);
    }
    else if($type == 'not_assigned')
    {
      $notAssigned = Role::select('role_id','role_name')
      ->whereNotIn('role_id' , function($notSelected) use ($user_id, $location){
          $notSelected->select('role_id')
          ->from('user_roles')
          ->where('user_id', $user_id)
          ->where('loc_id', $location);
      })->get();
      return response([ 'data' => $notAssigned]);
    }
  }


  public function save_roles(Request $request)
  {
    $user_id = $request->get('user_id');
    $loc_id = $request->get('loc_id');
    $roles = $request->get('roles');
    if($user_id != '')
    {
      DB::table('user_roles')
      ->where('user_id', '=', $user_id)
      ->where('loc_id', '=', $loc_id)
      ->delete();
      $user = UsrProfile::find($user_id);
      $save_roles = array();

      foreach($roles as $role)		{
        //array_push($save_roles, Role::find($role['role_id']));
        $user->roles()->save(Role::find($role['role_id']),['loc_id' => $loc_id]);
      }

      //$user->roles()->saveMany($save_roles,['loc_id' => $loc_id]);
      return response([
        'data' => [
          'user_id' => $user_id
        ]
      ]);
    }
    else {
      throw new ModelNotFoundException("Requested user not found", 1);
    }
  }


  public function locations(Request $request){
    $type = $request->type;
    $user_id = $request->user_id;

    if($type == 'assigned')
    {
      $assigned = Location::select('loc_id','loc_name')
      ->whereIn('loc_id' , function($selected) use ($user_id){
          $selected->select('loc_id')
          ->from('user_locations')
          ->where('user_id', $user_id);
      })->get();
      return response([ 'data' => $assigned]);
    }
    else if($type == 'not_assigned')
    {
      $notAssigned = Location::select('loc_id','loc_name')
      ->whereNotIn('loc_id' , function($notSelected) use ($user_id){
          $notSelected->select('loc_id')
          ->from('user_locations')
          ->where('user_id', $user_id);
      })->get();
      return response([ 'data' => $notAssigned]);
    }
  }


  public function save_locations(Request $request)
  {
    $user_id = $request->get('user_id');
    $locations = $request->get('locations');
    if($user_id != '')
    {
      DB::table('user_locations')->where('user_id', '=', $user_id)->delete();
      $user = UsrProfile::find($user_id);
      $save_locations = array();

      foreach($locations as $location)		{
        array_push($save_locations, Location::find($location['loc_id']));
      }

      $user->locations()->saveMany($save_locations);
      return response([
        'data' => [
          'user_id' => $user_id
        ]
      ]);
    }
    else {
      throw new ModelNotFoundException("Requested user not found", 1);
    }
  }


  public function user_assigned_locations(){
    $user_id = auth()->user()->user_id;
    $locations = DB::select("SELECT user_locations.*, org_location.loc_code, org_location.loc_name FROM user_locations
      INNER JOIN org_location ON org_location.loc_id = user_locations.loc_id WHERE user_locations.user_id = ?" , [$user_id]);
    return response([
      'data' => $locations
    ]);
  }


  //search Color for autocomplete
  private function autocomplete_search_has_login($search)
  {
    $user_lists = UsrProfile::select('usr_profile.user_id','usr_profile.first_name','usr_profile.last_name','usr_profile.email',
    DB::raw('CONCAT(usr_profile.first_name,usr_profile.last_name) AS full_name') )
    ->join('usr_login', 'usr_login.user_id', '=', 'usr_profile.user_id')
    ->where([['usr_profile.first_name', 'like', '%' . $search . '%'],]) ->get();
    return $user_lists;
  }


  //get searched Colors for datatable plugin format
  private function datatable_search($data)
  {
    $start = $data['start'];
    $length = $data['length'];
    $draw = $data['draw'];
    $search = $data['search']['value'];
    $order = $data['order'][0];
    $order_column = $data['columns'][$order['column']]['data'];
    $order_type = $order['dir'];

    $user_list = UsrProfile::join('org_location', 'usr_profile.loc_id', '=', 'org_location.loc_id')
    ->join('org_departments', 'usr_profile.dept_id', '=', 'org_departments.dep_id')
    ->join('org_designation','usr_profile.desig_id','=','org_designation.des_id')
    //->join('org_location', 'usr_profile.loc_id', '=', 'org_location.loc_id')
    ->select('usr_profile.*','org_location.loc_name','org_departments.dep_name','org_designation.des_name')
    ->where('first_name'  , 'like', $search.'%' )
    ->orWhere('last_name'  , 'like', $search.'%' )
    ->orWhere('emp_number'  , 'like', $search.'%' )
    ->orderBy($order_column, $order_type)
    ->offset($start)->limit($length)->get();

    $user_count = UsrProfile::join('org_location', 'usr_profile.loc_id', '=', 'org_location.loc_id')
    ->join('org_departments', 'usr_profile.dept_id', '=', 'org_departments.dep_id')
    ->join('org_designation','usr_profile.desig_id','=','org_designation.des_id')
    ->where('first_name'  , 'like', $search.'%' )
    ->orWhere('last_name'  , 'like', $search.'%' )
    ->orWhere('emp_number'  , 'like', $search.'%' )
    ->count();

    return [
        "draw" => $draw,
        "recordsTotal" => $user_count,
        "recordsFiltered" => $user_count,
        "data" => $user_list
    ];
  }


}
