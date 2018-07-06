<?php

namespace App\Http\Controllers\Org\Location;

use Illuminate\Http\Request;
use App\Models\Org\Location\Main_Cluster;
use App\Http\Controllers\Controller;

class MainClusterController extends Controller
{

	public function loaddata()
	{

		$cluster_list = Main_Cluster::join('org_source', 'org_group.source_id', '=', 'org_source.source_id')
		->select('org_group.*', 'org_source.source_code')
		->get();
		echo json_encode($cluster_list);

	}


	public function check_code(Request $request)
 {


  $count = Main_Cluster::where('group_code','=',$request->code)->count();
 // print_r($count);

  if($request->idcode > 0){

    $user = Main_Cluster::where('group_id', $request->idcode)->first();
     //print_r($user);
    if($user->group_code == $request->code)
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
    $main_cluster = new Main_Cluster();       
    if ($main_cluster->validate($request->all()))   
    {
      if($request->cluster_hid > 0){
        $main_cluster = Main_Cluster::find($request->cluster_hid);
      }     
      $main_cluster->fill($request->all());
      $main_cluster->status = 1;
      $main_cluster->created_by = 1;  
      $result = $main_cluster->saveOrFail();
           // echo json_encode(array('Saved'));
      echo json_encode(array('status' => 'success' , 'message' => 'Cluster details saved successfully.') );
    }
    else
    {            
            // failure, get errors
      $errors = $main_cluster->errors();
      echo json_encode(array('status' => 'error' , 'message' => $errors));
    }        


  }


  public function edit(Request $request)
	{

		 $group_id = $request->group_id;

		$cluster = Main_Cluster::join('org_source', 'org_group.source_id', '=', 'org_source.source_id')
		 			->select('org_group.*', 'org_source.source_code', 'org_source.source_name')
		 			->where('org_group.group_id', '=', $group_id)->get();
		 			echo json_encode($cluster);

 
	}


	public function delete(Request $request)
		{
 		 	$group_id = $request->group_id;
  			$cluster = Main_Cluster::where('group_id', $group_id)->update(['status' => 0]);
 		 	echo json_encode(array('delete'));
		}





}