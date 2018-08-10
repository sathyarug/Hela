<?php

namespace App\Http\Controllers\Org;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BuyerController extends Controller
{
    public function index(){
        return view('org.buyer.customer');
    }
}
