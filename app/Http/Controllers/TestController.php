<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Mockery\CountValidator\Exception;

class TestController extends Controller {

    public function index() {

    }

    public function auth(Request $request) {
        return json_encode(array(
          'status' => 'success',
          'message' => 'You successfully loged In'
        ));
    }







}
