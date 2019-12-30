<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Libraries\CapitalizeAllFields;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Merchandising\Costing\Costing;
use App\Models\Org\Section;


class CommonController extends Controller
{ 

    public function index(Request $request)
    {
      $type = $request->type;
      if($type == 'costing_id')    {
        $search = $request->search;
        return response($this->costing_autocomplete_search($search));
      }else if($type == 'user_loc')    {
        $search = $request->search;
        return response($this->usr_loc_autocomplete_search($search));
      }else if($type == 'loc_stores')    {
        return response($this->usr_stores_autocomplete_search($request));
      }
    }

    private function costing_autocomplete_search($search)
    {
      $lists = Costing::select('id')
      ->where([['id', 'like', '%' . $search . '%'],]) ->get();
      return $lists;
    }

    private function usr_loc_autocomplete_search($search)
    {
      $query = DB::table('user_locations')
      ->join('org_location','user_locations.loc_id','=','org_location.loc_id')
      ->select('user_locations.loc_id','org_location.loc_name')
      ->where([['loc_name', 'like', '%' . $search . '%'],])
      ->where('org_location.status', 1)
      ->where('user_locations.user_id',auth()->payload()['user_id']) 
      ->get();
      return $query;
    }

    private function usr_stores_autocomplete_search($request)
    {
      $query = DB::table('org_store')
      ->select('org_store.store_id','org_store.store_name') 
      ->where('org_store.store_name', 'like', '%' . $request->search . '%')
      ->where('org_store.status', 1)
      ->where('org_store.loc_id',$request->location) 
      ->get();
      return $query;
    }


}
