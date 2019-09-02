<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\UsrProfile;
use App\User;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'logout']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['user_name', 'password']);

        $customData = $this->get_user_from_username($credentials['user_name']);

        if (!$token = auth()->claims($customData)->setTTL(720)->attempt($credentials)) {
            //return response()->json(['error' => 'Unauthorized'], 401);
              return response()->json(['error' => 'Unauthorized' , 'message' => 'Incorrect username or password'], 401);
        }
        else{
          if($customData['loc_id'] != 0){
            //save token to database
            $user = User::find(auth()->user()->user_id);
            $user->token = auth()->payload()->get('jti');
            $user->save(); //update token in user_login table

            $this->store_load_permissions($customData['user_id'], $customData['loc_id']);
            return $this->respondWithToken($token, $customData['loc_id']);
          }
          else{
            return response()->json(['error' => 'Unauthorized' , 'message' => 'No location assigned for user'], 401);
          }
        }

    }

    /**
    * Get the authenticated User.
    *
    * @return \Illuminate\Http\JsonResponse
    */
   public function me()
   {
       return response()->json(auth()->user());
   }

   /**
    * Log the user out (Invalidate the token).
    *
    * @return \Illuminate\Http\JsonResponse
    */
   public function logout()
   {
       $user_id = auth()->user()->user_id;
       $user = User::find($user_id);
       $user->token = null;
       $user->save(); //update token in user_login table

       auth()->logout();
       DB::table('usr_login_permission')->where('user_id', '=', $user_id)->delete();
       return response()->json(['message' => 'Successfully logged out']);
   }

   /**
    * Refresh a token.
    *
    * @return \Illuminate\Http\JsonResponse
    */
   public function refresh(Request $request)
   {
     $loc_id = $request->loc_id;
     $user = UsrProfile::find(auth()->user()->user_id);
     $user->loc_id = $loc_id;
     $user = $user->toArray();
     // $customData = $this->get_user_from_username($credentials['user_name']);
     $this->store_load_permissions($user['user_id'], $loc_id);
     return $this->respondWithTokenRefresh(auth()->claims($user)->setTTL(720)->refresh(), $loc_id);
   }

   /**
    * Get the token array structure.
    *
    * @param  string $token
    *
    * @return \Illuminate\Http\JsonResponse
    */
   protected function respondWithToken($token, $location)
   {
       $user_id = auth()->user()->user_id;
       $user = UsrProfile::find($user_id);
       $user_data = [
         'user_id' => $user->user_id,
         'location' => $location,
         'first_name' => $user->first_name,
         'last_name' => $user->last_name
       ];

       $permissions = DB::table('usr_login_permission')->where('user_id' , '=', $user_id)->pluck('permission_code');

       return response()->json([
           'access_token' => $token,
           'token_type' => 'bearer',
           'expires_in' => auth()->factory()->getTTL(),
           'user' => $user_data,//auth()->user()
           'permissions' => $permissions
       ]);
   }


   protected function respondWithTokenRefresh($token, $loc_id)
   {
       $user_id = auth()->user()->user_id;
       $user = UsrProfile::find($user_id);
       $user_data = [
         'user_id' => $user->user_id,
         'location' => $loc_id,
         'first_name' => $user->first_name,
         'last_name' => $user->last_name
       ];

       $permissions = DB::table('usr_login_permission')->where('user_id' , '=', $user_id)->pluck('permission_code');

       return response()->json([
           'access_token' => $token,
           'token_type' => 'bearer',
           'expires_in' => auth()->factory()->getTTL(),
           'user' => $user_data,//auth()->user()
           'permissions' => $permissions
       ]);
   }


   private function get_user_from_username($username){
     $customData = UsrProfile::select('usr_profile.user_id', 'usr_profile.dept_id')
     ->join('usr_login','usr_login.user_id','=','usr_profile.user_id')
     ->where('usr_login.user_name','=',$username)
     ->first();
     $customData = ($customData == null) ? [] : $customData->toArray();

     $default_location = DB::table('user_locations')->where('user_id' , '=', $customData['user_id'])->first();
     if($default_location != null){
      $customData['loc_id'] = $default_location->loc_id;
     }
     else{
         $customData['loc_id'] = 0;
     }
     return $customData;
   }


   private function store_load_permissions($user_id, $location){
     DB::table('usr_login_permission')->where('user_id', '=', $user_id)->delete();
     DB::insert("INSERT INTO usr_login_permission(user_id, permission_code)
      SELECT user_roles.user_id, permission_role_assign.permission FROM permission_role_assign
      INNER JOIN user_roles ON user_roles.role_id = permission_role_assign.role
      WHERE user_roles.user_id = ? AND user_roles.loc_id = ? GROUP BY permission_role_assign.permission", [$user_id, $location]);
   }

 }
