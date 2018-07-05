<?php

namespace App\Http\Controllers\Org\Location;

use Illuminate\Http\Request;
use App\Models\Org\Location\Main_Location;
use App\Models\Org\Location\Main_Cluster;
use App\Models\Org\Location\Currency;
use App\Models\Org\Location\Country;
use App\Http\Controllers\Controller;

class MainLocationController extends Controller
{

	public function loaddata()
	{

		$location_list = Main_Location::join('org_group', 'org_company.group_id', '=', 'org_group.group_id')
		->select('org_company.*', 'org_group.group_code')
		->get();
		echo json_encode($location_list);

	}


	public function select_loc_list(Request $request){

		$search_c = $request->search;
  		//print_r($search_c);
		$loc_lists = Main_Cluster::select('group_id','group_code','group_name')
		->where([['status', '=', '1'],['group_name', 'like', '%' . $search_c . '%'],]) ->get();


		return response()->json(['items'=>$loc_lists]);
    		//return $select_source;

	}


	public function load_currency(Request $request){

		$search_c = $request->search;
  		//print_r($search_c);
		$curr_lists = Currency::select('currency_id','currency_code','currency_description')
		->where([['currency_description', 'like', '%' . $search_c . '%'],]) ->get();


		return response()->json(['items'=>$curr_lists]);
    		//return $select_source;

	}

	public function load_country(Request $request){

		$search_c = $request->search;
  		//print_r($search_c);
		$country_lists = Country::select('country_id','country_code','country_description')
		->where([['country_description', 'like', '%' . $search_c . '%'],]) ->get();


		return response()->json(['items'=>$country_lists]);
    		//return $select_source;

	}


	public function check_code(Request $request)
	{


		$count = Main_Location::where('company_code','=',$request->code)->count();
 // print_r($count);

		if($request->idcode > 0){

			$user = Main_Location::where('company_id', $request->idcode)->first();
     //print_r($user);
			if($user->company_code == $request->code)
			{
				$msg = true;

			}else{

				$msg = 'Can not change. please try again';

			}


		}else{

			if($count == 1){ 

				$msg = 'Already exists. please try another one'; 

			}else{ 

				$msg = true; 

			}

		}
		echo json_encode($msg);
	}




	public function postdata(Request $request)
	{
  	//print_r($request->cluster_hid);
		$main_location = new Main_Location();       
		if ($main_location->validate($request->all()))   
		{
			if($request->location_hid > 0){
				$main_location = Main_Location::find($request->location_hid);
			}     
			$main_location->fill($request->all());
			$main_location->status = 1;
			$main_location->created_by = 1;  
			$result = $main_location->saveOrFail();
           // echo json_encode(array('Saved'));
			echo json_encode(array('status' => 'success' , 'message' => 'Source details saved successfully.') );
		}
		else
		{            
            // failure, get errors
			$errors = $main_location->errors();
			echo json_encode(array('status' => 'error' , 'message' => $errors));
		}        


	}


	public function edit(Request $request)
	{

		$loc_id = $request->loc_id;

		$cluster = Main_Location::join('org_group', 'org_company.group_id', '=', 'org_group.group_id')
		->join('org_country', 'org_company.country_code', '=', 'org_country.country_id')
		->join('fin_currency', 'org_company.default_currency', '=', 'fin_currency.currency_id')
		->select('org_company.*', 'org_group.group_code', 'org_group.group_name', 'org_country.country_description', 'fin_currency.currency_description')
		->where('org_company.company_id', '=', $loc_id)->get();
		echo json_encode($cluster);


	}


	public function delete(Request $request)
	{
		$loc_id = $request->loc_id;

		$cluster = Main_Location::where('company_id', $loc_id)->update(['status' => 0]);
		echo json_encode(array('delete'));
	}




}