<?php

namespace App\Http\Controllers\stores;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GrnController extends Controller
{
    public function grnDetails() {
        return view('grn.grn_details');
        
    }

    public function index(Request $request){
        echo 'sasasa';
        dd($request); exit;
    }

    public function store(Request $request){
        //echo 'aaa';
        //dd($request); exit;

        foreach ($request['item_list'] as $rec){
            //dd($rec['master_description']);
            if($rec['item_select']){

            }
        }
    }
}
