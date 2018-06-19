<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GrnController extends Controller
{
    public function grnDetails() {
        return view('grn.grn_details');
        
    }
}
