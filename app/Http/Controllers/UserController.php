<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

use App\Http\Requests\UserRequest;

class UserController extends Controller {

    public function store(UserRequest $request)
    {

        $data = request()->except(['_token']);
        $profile = new \App\UsrProfile;
        $login = new \App\User;

        $profile->fill($request->all());

        $login->user_name = $request->user_name;
        $login->password = Hash::make($request->password);

        if ($profile->validate($request->all()) && $login->validate($request->all())){
            $profile->save();

           //Adding user id user login table
           $login->user_id = $profile->user_id;
           $login->save();
        }else{
           $errors = $profile->errors_tostring();
           return $errors;
        }

    }
    

}
