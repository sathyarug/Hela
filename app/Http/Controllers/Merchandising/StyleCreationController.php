<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\OrgSupplier;

class StyleCreationController extends Controller
{
    public function getList() {
        return datatables()->of(OrgSupplier::all()->sortByDesc("supplier_id")->sortByDesc("status"))->toJson();
    }
}
