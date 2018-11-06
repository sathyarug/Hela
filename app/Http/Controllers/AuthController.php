<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

use App\UsrProfile;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
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

        if (! $token = auth()->claims($customData)->attempt($credentials)) {
            //return response()->json(['error' => 'Unauthorized'], 401);
              return response()->json(['error' => 'Unauthorized' , 'message' => 'Incorrect username or password'], 401);
        }

        return $this->respondWithToken($token);
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
       auth()->logout();

       return response()->json(['message' => 'Successfully logged out']);
   }

   /**
    * Refresh a token.
    *
    * @return \Illuminate\Http\JsonResponse
    */
   public function refresh()
   {
       return $this->respondWithToken(auth()->refresh());
   }

   /**
    * Get the token array structure.
    *
    * @param  string $token
    *
    * @return \Illuminate\Http\JsonResponse
    */
   protected function respondWithToken($token)
   {
       $user_id = auth()->user()->user_id;
       $user = UsrProfile::find($user_id);
       $user_data = [
         'user_id' => $user->user_id,
         'location' => $user->loc_id,
         'first_name' => $user->first_name,
         'last_name' => $user->last_name
       ];
       return response()->json([
           'access_token' => $token,
           'token_type' => 'bearer',
           'expires_in' => auth()->factory()->getTTL() * 360,
           'user' => $user_data//auth()->user()
       ]);
   }

   private function get_user_from_username($username){
     $customData = UsrProfile::select('usr_profile.loc_id','usr_profile.dept_id')
     ->join('usr_login','usr_login.user_id','=','usr_profile.user_id')
     ->where('usr_login.user_name','=',$username)
     ->first();
     $customData = ($customData == null) ? [] : $customData->toArray();
     return $customData;
   }

 }
