<?php

namespace App\Http\Controllers\Org;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;
use App\Models\Org\Country;
use App\Http\Resources\Org\CountryResource;

class CountryController extends Controller
{
    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => []]);
    }

    //Display a listing of the resource.
    public function index(Request $request)
    {
       $type = $request->type;

       if($type == 'datatable')   {
         $data = $request->all();
         return response($this->datatable_search($data));
       }
       else if($type == 'auto')    {
         $search = $request->search;
         return response($this->autocomplete_search($search));
       }
       else{
         return response([]);
       }
    }


    //create new country
    public function store(Request $request)
    {
      try
      {
          $country = new Country();
          $country->country_code = $request->country_code;
          $country->country_description = $request->country_description;
          $country->status = 1;
          $country->save();

          return response([
            'data' => [
              'message' => 'Country updated successfully',
              'country' => $country
            ]
          ] , Response::HTTP_CREATED );

      } catch (Exception $e) {
          return response([
            'errors' => [
              'userMessage' => 'Error occured while creating country',
              'internalMessage' => $e
            ]
          ] , Response::HTTP_INTERNAL_SERVER_ERROR);
      }
    }


    //get new country
    public function show($id)
    {
        try
        {
          $country = Country::find($id);
          if($country == null)
            return response( ['data' => 'Requested country not found'] , Response::HTTP_NOT_FOUND );
          else
            return response( ['data' => $country] );
        }
        catch(Exception $e) {
          return response( ['errors' => [
              'userMessage' => 'Error occured finding requested country',
              'internalMessage' => $e
            ] ] , Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    //update country
    public function update(Request $request, $id)
    {
      try {
          $country = Country::find($id);
          $country->country_description = $request->country_description;
          $country->save();

          return response([
            'data' => [
              'message' => 'Country saved successfully',
              'country' => $country
            ]
          ]);

      } catch (Exception $e) {
        return response([ 'errors' => [
          'userMessage' => 'Error occured updating country',
          'internalMessage' => $e
          ]
        ] , Response::HTTP_INTERNAL_SERVER_ERROR);
      }
    }


    //deactivate a country
    public function destroy($id)
    {
      try{
        $country = Country::where('country_id', $id)->update(['status' => 0]);
        return response([
          'data' => [
            'message' => 'Country was deactivated successfully',
            'country' => $country
          ]
        ]);
      }
      catch(Exception $e){
        return response([
          'errors' => [
            'userMessage' => 'Error occured while deleting country',
            'internalMessage' => $e
            ]
        ] , Response::HTTP_INTERNAL_SERVER_ERROR);
      }
    }


    //validate anything based on requirements
    public function validate_data(Request $request){
      try{
        $for = $request->for;
        if($for == 'duplicate')
        {
          return response($this->validate_duplicate_code($request->goods_type_id , $request->goods_type_description));
        }
      }
      catch(Exception $e){
        return response([
          'errors' => [
            'userMessage' => 'Error occured while validating',
            'internalMessage' => $e
            ]
        ] , Response::HTTP_INTERNAL_SERVER_ERROR);
      }
    }


    //check country code
    public function validate_duplicate_code($country_id , $country_code)
    {
      $country = Country::where('country_code','=',$country_code)->first();
      if($country == null){
        return response(['status' => 'success']);
      }
      else if($country->country_id == $country_id){
        return response(['status' => 'success']);
      }
      else {
        return response(['status' => 'error','message' => 'Country code already exists']);
      }
    }


    //search countries for autocomplete
    private function autocomplete_search($search)
  	{
  		$country_lists = Country::select('country_id','country_code','country_description')
  		->where([['country_description', 'like', '%' . $search . '%'],])
      ->get();
  		return $country_lists;
  	}


    //get searched countries for datatable plugin format
    private function datatable_search($data)
    {

      $start = $data['start'];
      $length = $data['length'];
      $draw = $data['draw'];
      $search = $data['search']['value'];
      $order = $data['order'][0];
      $order_column = $data['columns'][$order['column']]['data'];
      $order_type = $order['dir'];

      $country_list = Country::select('*')
      ->where('country_code'  , 'like', $search.'%' )
      ->orWhere('country_description'  , 'like', $search.'%' )
      ->orderBy($order_column, $order_type)
      ->offset($start)->limit($length)->get();

      $country_count = Country::where('country_code'  , 'like', $search.'%' )
      ->orWhere('country_description'  , 'like', $search.'%' )
      ->count();

      return [
          "draw" => $draw,
          "recordsTotal" => $country_count,
          "recordsFiltered" => $country_count,
          "data" => $country_list
      ];
    }

}
