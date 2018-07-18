<?php

namespace App\Http\Controllers\Org\Location;

use Illuminate\Http\Request;
use App\Models\Org\Location\Main_Location;
use App\Models\Org\Location\Main_Cluster;
use App\Currency;
use App\Country;
use App\Models\Org\Location\Main_Sub_Location;
use App\Http\Controllers\Controller;
use App\TypeOfLocation;
use App\Models\Finance\Accounting\CostCenter;
use App\TypeOfProperty;
use App\Models\Org\Location\LocationCostCenter;

class MainSubLocationController extends Controller
{

	public function loaddata()
	{

		$sub_location_list = Main_Sub_Location::join('org_company', 'org_location.company_id', '=', 'org_company.company_id')
		->select('org_location.*', 'org_company.company_name')
		->get();
		echo json_encode($sub_location_list);

	}


	public function load_list(Request $request){

		$search_c = $request->search;
  		//print_r($search_c);
		$loc_lists = Main_Location::select('company_id','company_code','company_name')
		->where([['status', '=', '1'],['company_name', 'like', '%' . $search_c . '%'],]) ->get();


		return response()->json(['items'=>$loc_lists]);
    		//return $select_source;

	}

	public function type_of_loc(Request $request){

		$search_c = $request->search;
  		//print_r($search_c);
		$type_of_list = TypeOfLocation::select('type_loc_id','type_location')
		->where([['type_location', 'like', '%' . $search_c . '%'],]) ->get();


		return response()->json(['items'=>$type_of_list]);
    		//return $select_source;

	}

	public function load_cost_center(Request $request){

		$search_c = $request->search;
  		//print_r($search_c);
		$type_of_list = CostCenter::select('cost_center_id','cost_center_name')
		->where([['cost_center_name', 'like', '%' . $search_c . '%'],]) ->get();


		return response()->json(['items'=>$type_of_list]);
    		//return $select_source;

	}

	public function load_property(Request $request){

		$search_c = $request->search;
  		//print_r($search_c);
		$type_of_list = TypeOfProperty::select('type_prop_id','type_property')
		->where([['type_property', 'like', '%' . $search_c . '%'],]) ->get();


		return response()->json(['items'=>$type_of_list]);
    		//return $select_source;

	}

	public function check_code(Request $request)
	{


		$count = Main_Sub_Location::where('loc_code','=',$request->code)->count();
 // print_r($count);

		if($request->idcode > 0){

			$user = Main_Sub_Location::where('loc_id', $request->idcode)->first();
     //print_r($user);
			if($user->loc_code == $request->code)
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
		$main_sub_location = new Main_Sub_Location();       
		if ($main_sub_location->validate($request->all()))   
		{
			if($request->sub_location_hid > 0){
				$main_sub_location = Main_Sub_Location::find($request->sub_location_hid);
			}     
			$main_sub_location->fill($request->all());
			$main_sub_location->status = 1;
			$main_sub_location->created_by = 1;  
			$result = $main_sub_location->saveOrFail();
            $insertedId = $main_sub_location->loc_id;

			//OrgCompanySection::where('company_id', '=', $insertedId)->update(['status' => 0]);
			$multipleValues = $request->get('type_center');

			if($multipleValues != ''){
  			foreach($multipleValues as $value)
   			{
          	$Cost_Center = new LocationCostCenter();  
			$Cost_Center->loc_id = $insertedId;
			$Cost_Center->cost_name = $value;
			$Cost_Center->status = 1;
			$Cost_Center->created_by = 1;  
			$result = $Cost_Center->saveOrFail();

			}} 

			echo json_encode(array('status' => 'success' , 'message' => 'Source details saved successfully.') );
		}
		else
		{            
            // failure, get errors
			$errors = $main_sub_location->errors();
			echo json_encode(array('status' => 'error' , 'message' => $errors));
		}        


	}


	public function edit(Request $request)
	{

		$company_id = $request->company_id;

		$sub_location = Main_Sub_Location::join('org_company', 'org_location.company_id', '=', 'org_company.company_id')
					 ->join('org_country', 'org_location.country_code', '=', 'org_country.country_id')
					 ->join('fin_currency', 'org_location.currency_code', '=', 'fin_currency.currency_id')
					 ->join('type_of_location', 'org_location.type_of_loc', '=', 'type_of_location.type_loc_id')
					 ->join('type_of_property', 'org_location.type_property', '=', 'type_of_property.type_prop_id')
					 ->select('org_location.*', 'org_company.company_name', 'org_country.country_description', 'fin_currency.currency_description','type_of_location.type_location','type_of_property.type_property')
					->where('org_location.loc_id', '=', $company_id)->get();
					echo json_encode($sub_location);


	}


	public function delete(Request $request)
	{
		$company_id = $request->company_id;

		$sub_location = Main_Sub_Location::where('loc_id', $company_id)->update(['status' => 0]);
		echo json_encode(array('delete'));
	}




}