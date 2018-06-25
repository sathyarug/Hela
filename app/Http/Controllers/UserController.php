<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests\UserRequest;

class UserController extends Controller {

    //
    public function store(UserRequest $request) {

        //dd($request->all()); exit;
           
        

        $data = request()->except(['_token']);
        /*$data['last_name'] = 'Mapalagama';
        $data['loc_id'] = 1;
        $data['dept_id'] = 1;
        $data['cost_center_id'] = 1;*/
        
        $user = new \App\UsrProfile;
        $user->fill($request->all());
        $user->save();
        
        
        //echo '<pre>'.print_r($data, true).'</pre>';
        
        //
    }
    

}
