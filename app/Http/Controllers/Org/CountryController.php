<?php

namespace App\Http\Controllers\Org;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;
use App\Models\Org\Country;
use App\Http\Resources\Org\CountryResource;
use App\Libraries\AppAuthorize;

class CountryController extends Controller
{
    var $authorize = null;

    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
      $this->authorize = new AppAuthorize();
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
      if($this->authorize->hasPermission('COUNTRY_MANAGE'))//check permission
      {
        $country = new Country();
        if($country->validate($request->all()))
        {
          $country->fill($request->all());
          $country->status = 1;
          $country->save();
          return response([
            'data' => [
              'message' => 'Country saved successfully',
              'country' => $country
            ]
          ] , Response::HTTP_CREATED );
        }
        else{
          $errors = $department->errors();// failure, get errors
          return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //get new country
    public function show($id)
    {
      if($this->authorize->hasPermission('COUNTRY_MANAGE'))//check permission
      {
        $country = Country::find($id);
        if($country == null)
          return response( ['data' => 'Requested country not found'] , Response::HTTP_NOT_FOUND );
        else
          return response( ['data' => $country] );
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //update country
    public function update(Request $request, $id)
    {
      if($this->authorize->hasPermission('COUNTRY_MANAGE'))//check permission
      {
        $country = Country::find($id);
        if($country->validate($request->all()))
        {
          $country->fill($request->except('country_code'));
          $country->save();

          return response([
            'data' => [
              'message' => 'Country updated successfully',
              'country' => $country
            ]
          ]);
        }
        else
        {
          $errors = $country->errors();// failure, get errors
          return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //deactivate a country
    public function destroy($id)
    {
      if($this->authorize->hasPermission('COUNTRY_DELETE'))//check permission
      {
        $country = Country::where('country_id', $id)->update(['status' => 0]);
        return response([
          'data' => [
            'message' => 'Country was deactivated successfully.',
            'country' => $country
          ]
        ] , Response::HTTP_NO_CONTENT);
      }
      else{
        return response($this->authorize->error_response(), 401);
      }
    }


    //validate anything based on requirements
    public function validate_data(Request $request){
        $for = $request->for;
        if($for == 'duplicate')
        {
          return response($this->validate_duplicate_code($request->country_id , $request->country_code));
        }

    }


    //check country code
    public function validate_duplicate_code($country_id , $country_code)
    {
      $country = Country::where('country_code','=',$country_code)->first();
      if($country == null){
        return ['status' => 'success'];
      }
      else if($country->country_id == $country_id){
        return ['status' => 'success'];
      }
      else {
        return ['status' => 'error','message' => 'Country code already exists'];
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
      if($this->authorize->hasPermission('COUNTRY_MANAGE'))//check permission
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
      else{
        return response($this->authorize->error_response(), 401);
      }
    }

}
