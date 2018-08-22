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
      $this->middleware('jwt.verify', ['except' => ['list']]);
    }

    //Display a listing of the resource.
    public function index()
    {
        return CountryResource::collection(Country::all());
    }


    //create new country
    public function store(Request $request)
    {
      try {

          $country = new Country();
          $country->country_code = $request->country_code;
          $country->country_description = $request->country_description;
          $country->status = 1;
          $country->save();

          return response([
            'status' => 'success',
            'message' => 'Country updated successfully',
            'country' => $country
          ] , Response::HTTP_CREATED );

      } catch (Exception $e) {
          return response([
            'status' => 'error',
            'message' => 'Country updating process error'
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
            return response( ['data' => []] , Response::HTTP_NOT_FOUND );
          else
            return response( ['data' => $country] );
        }
        catch(Exception $e){
          return response( ['data' => $country] , Response::HTTP_INTERNAL_SERVER_ERROR);
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
            'status' => 'success',
            'message' => 'Country saved successfully',
            'country' => $country
          ]);

      } catch (Exception $e) {
          return response([
            'status' => 'error',
            'message' => 'Country saving process error'
          ] , Response::HTTP_INTERNAL_SERVER_ERROR);
      }
    }


    //deactivate a country
    public function destroy($id)
    {
      try{
        $country = Country::where('country_id', $id)->update(['status' => 0]);
        return response([
          'status' => 'success',
          'message' => 'Country was deactivated successfully.'
        ]);
      }
      catch(Exception $e){
        return response([
          'status' => 'error',
          'message' => ''
        ] , Response::HTTP_INTERNAL_SERVER_ERROR);
      }
    }


    //check country code
    public function check_code(Request $request)
    {
      $country = Country::where('country_code','=',$request->country_code)->first();
      if($country == null){
        return response(['status' => 'success']);
      }
      else if($country->country_id == $request->country_id){
        return response(['status' => 'success']);
      }
      else {
        return response(['status' => 'error','message' => 'Country code already exists']);
      }
    }


    //search countries
    public function search(Request $request)
  	{
  		$search_c = $request->search;
  		$country_lists = Country::select('country_id','country_code','country_description')
  		->where([['country_description', 'like', '%' . $search_c . '%'],]) ->get();
  		return response()->json($country_lists);
  	}


    //get searched countries for datatable plugin format
    public function list(Request $request)
    {
      $data = $request->all();
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

      echo json_encode(array(
          "draw" => $draw,
          "recordsTotal" => $country_count,
          "recordsFiltered" => $country_count,
          "data" => $country_list
      ));
    }

}
