<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\CapitalizeAllFields;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Merchandising\Costing\Costing;

class CommonController extends Controller
{ 

    public function index(Request $request)
    {
      $type = $request->type;
      if($type == 'costing_id')    {
        $search = $request->search;
        return response($this->costing_autocomplete_search($search));
      }
    }

    private function costing_autocomplete_search($search)
    {
      $lists = Costing::select('id')
      ->where([['id', 'like', '%' . $search . '%'],]) ->get();
      return $lists;
    }


}
