<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Cookie;


//use Html;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }


    public function showLogin()
    {
        // show the form
        return view('login');
    }

    public function doLogin(Request $request)
    {
        // validate the info, create rules for the inputs
        $rules = array(
      //   'user-name'    => 'required|email', // make sure the email is an actual email
            'password' => 'required|alphaNum|min:3' // password can only be alphanumeric and has to be greater than 3 characters
        );

        // run the validation rules on the inputs from the form
        $validator = Validator::make(Input::all(), $rules);

        // if the validator fails, redirect back to the form
        if ($validator->fails()) {
            return Redirect::to('/')
                ->withErrors($validator) // send back all errors to the login form
                ->withInput(Input::except('password')); // send back the input (not the password) so that we can repopulate the form
        } else {

            // create our user data for the authentication
            $userdata = array(
                'user_name' 	=> Input::get('user-name'),
                'password' 	=> Input::get('password')
            );

            $remember = (Input::has('remember')) ? true : false;

            // attempt to do the login
            if (Auth::attempt($userdata,$remember)) {
                if($remember){
                    Cookie::queue("user-name", Input::get('user-name'), 3600);
                    Cookie::queue("password", Input::get('password'), 3600);
                }else{
                    Cookie::queue(Cookie::forget('user-name'));
                    Cookie::queue(Cookie::forget('password'));
                }
                return Redirect::to('/home');

            } else {
                // validation not successful, send back to form
                $request->session()->flash('loginError', trans('auth.failed'));
                return Redirect::to('/');

            }

        }
    }

    public function logout(Request $request) {
        Auth::logout();
        return redirect('/');
    }

}
