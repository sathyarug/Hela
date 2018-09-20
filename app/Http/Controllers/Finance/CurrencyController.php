<?php

namespace App\Http\Controllers\Finance;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use App\Models\Finance\Currency;

class CurrencyController extends Controller
{
    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
    }

    //get shipment term list
    public function index(Request $request)
    {
      $type = $request->type;

      if($type == 'datatable') {
        $data = $request->all();
        return response($this->datatable_search($data));
      }
      else if($type == 'auto') {
        $search = $request->search;
        return response($this->autocomplete_search($search));
      }
      else {
        $active = $request->active;
        $fields = $request->fields;
        return response([
          'data' => $this->list($active , $fields)
        ]);
      }
    }

    //create a shipment term
    public function store(Request $request)
    {
      $currency = new Currency();
      if ($currency->validate($request->all()))
      {
        $currency->fill($request->all());
        $currency->status = 1;
        $currency->save();

        return response([ 'data' => [
          'message' => 'Currency was saved successfully',
          'currency' => $currency
          ]
        ], Response::HTTP_CREATED );
      }
      else
      {
        $errors = $customer->errors();// failure, get errors
        return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }

    //get shipment term
    public function show($id)
    {
        $currency = Currency::find($id);
        if($currency == null)
          throw new ModelNotFoundException("Requested currency not found", 1);
        else
          return response([ 'data' => $currency ]);
    }


    //update a shipment term
    public function update(Request $request, $id)
    {
        $currency = Currency::find($id);
        if ($currency->validate($request->all()))
        {
          $currency->fill($request->except('currency_code'));
          $currency->save();

          return response([ 'data' => [
            'message' => 'Currency was updated successfully',
            'currency' => $currency
          ]]);
        }
        else
        {
          $errors = $customer->errors();// failure, get errors
          return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    //deactivate a ship term
    public function destroy($id)
    {
        $currency = Currency::where('currency_id', $id)->update(['status' => 0]);
        return response([
          'data' => [
            'message' => 'Currency was deactivated successfully.',
            'shipTerm' => $currency
          ]
        ] , Response::HTTP_NO_CONTENT);
    }


    //validate anything based on requirements
    public function validate_data(Request $request){
      $for = $request->for;
      if($for == 'duplicate')
      {
        return response($this->validate_duplicate_code($request->currency_id , $request->currency_code));
      }
    }


    //check shipment cterm code code already exists
    private function validate_duplicate_code($id , $code)
    {
      $currency = Currency::where('currency_code','=',$code)->first();
      if($currency == null){
        return ['status' => 'success'];
      }
      else if($currency->currency_id == $id){
        return ['status' => 'success'];
      }
      else {
        return ['status' => 'error','message' => 'Currency code already exists'];
      }
    }


    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
      $query = null;
      if($fields == null || $fields == '') {
        $query = Currency::select('*');
      }
      else{
        $fields = explode(',', $fields);
        $query = Currency::select($fields);
        if($active != null && $active != ''){
          $query->where([['status', '=', $active]]);
        }
      }
      return $query->get();
    }


    //search shipment terms for autocomplete
    private function autocomplete_search($search)
  	{
  		$ship_term_lists = Currency::select('currency_id','currency_code')
  		->where([['currency_code', 'like', '%' . $search . '%'],]) ->get();
  		return $ship_term_lists;
  	}


    //get searched ship terms for datatable plugin format
    private function datatable_search($data)
    {
      $start = $data['start'];
      $length = $data['length'];
      $draw = $data['draw'];
      $search = $data['search']['value'];
      $order = $data['order'][0];
      $order_column = $data['columns'][$order['column']]['data'];
      $order_type = $order['dir'];

      $currency_list = Currency::select('*')
      ->where('currency_code'  , 'like', $search.'%' )
      ->orWhere('currency_description'  , 'like', $search.'%' )
      ->orderBy($order_column, $order_type)
      ->offset($start)->limit($length)->get();

      $currency_count = Currency::where('currency_code'  , 'like', $search.'%' )
      ->orWhere('currency_description'  , 'like', $search.'%' )
      ->count();

      return [
          "draw" => $draw,
          "recordsTotal" => $currency_count,
          "recordsFiltered" => $currency_count,
          "data" => $currency_list
      ];
    }

}
