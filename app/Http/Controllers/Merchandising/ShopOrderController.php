<?php

namespace App\Http\Controllers\Merchandising;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use App\Models\Merchandising\CustomerOrder;
use App\Models\Merchandising\Costing\Costing;
use App\Models\Merchandising\StyleCreation;
use App\Models\Merchandising\BOMHeader;
use App\Libraries\SearchQueryBuilder;

class ShopOrderController extends Controller
{
    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
    }

    //get customer list
    public function index(Request $request)
    {
      $type = $request->type;
      if($type == 'datatable') {
        $data = $request->all();
        return response($this->datatable_search($data));
      }
      else if($type == 'auto')    {
        $search = $request->search;
        return response($this->autocomplete_search($search));
      }
      else if($type == 'style')    {
        $search = $request->search;
        return response($this->style_search($search));
      }
      else if($type == 'search_fields'){
        return response([
          'data' => $this->get_search_fields()
        ]);
      }
      elseif($type == 'select') {
          $active = $request->active;
          $fields = $request->fields;
          return response([
              'data' => $this->list($active, $fields)
          ]);
      }
      else{
        return response([]);
      }
    }


    //create a customer
    public function store(Request $request)
    {

    }


    //get a customer
    public function show($id)
    {

    }


    //update a customer
    public function update(Request $request, $id)
    {

    }


    //deactivate a customer
    public function destroy($id)
    {

    }


    //validate anything based on requirements
    public function validate_data(Request $request)
    {

    }




    //check customer code already exists
    private function validate_duplicate_code($id , $code)
    {

    }


    //search customer for autocomplete
    private function autocomplete_search($search)
  	{

  	}


    //search customer for autocomplete
    private function style_search($search)
  	{
  		$shopOrder_lists = BOMHeader::select('item_master.master_id', 'item_master.master_code','item_master.master_description')
      ->join('item_master', 'bom_header.fng_id', '=', 'item_master.master_id')
  		->where([['master_code', 'like', '%' . $search . '%'],])
      ->get();

  		return $shopOrder_lists;
  	}

    public function load_shop_order_header(Request $request){

      $fng_id = $request->fng_id;

      $fng_country = BOMHeader::select('org_country.country_id', 'org_country.country_description')
                   ->join('bom_details', 'bom_details.bom_id', '=', 'bom_header.bom_id')
                   ->join('org_supplier', 'org_supplier.supplier_id', '=', 'bom_details.supplier_id')
                   ->join('org_country', 'org_country.country_id', '=', 'org_supplier.supplier_country')
                   ->where('fng_id', '=', $fng_id)
                   ->groupBy('bom_details.supplier_id')
                   ->get();

      $arr['country'] = $fng_country;

      if($arr == null)
          throw new ModelNotFoundException("Requested section not found", 1);
      else
          return response([ 'data' => $arr ]);

    }



    //get searched customers for datatable plugin format
    private function datatable_search($data)
    {

    }


    private function get_search_fields()
    {

    }

    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {

    }




}
