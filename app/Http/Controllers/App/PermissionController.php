<?php

namespace App\Http\Controllers\App;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use Exception;

class PermissionController extends Controller
{
    public function __construct()
    {
      $this->middleware('jwt.verify', ['except' => []]);
      //add functions names to 'except' paramert to skip authentication  
    }


    public function get_required_permissions(Request $request)
    {
        $result = DB::table('usr_login_permission')
        ->where('user_id', '=', auth()->user()->user_id)
        ->whereIn('permission_code', $request->permissions)
        ->get();
        $permissions = [];
        foreach ($result as $row) {
        //  echo json_encode($row);
          $permissions[$row->permission_code] = true;
        }
        return response([
          'data' => $permissions
        ]);
    }




}
