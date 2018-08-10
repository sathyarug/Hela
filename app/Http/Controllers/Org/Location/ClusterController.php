<?php

namespace App\Http\Controllers\Org\Location;

use Illuminate\Http\Request;
use App\Models\Org\Location\Cluster;
use App\Http\Controllers\Controller;

class ClusterController extends Controller
{

	public function get_list(Request $request)
	{
		$data = $request->all();
		$start = $data['start'];
		$length = $data['length'];
		$draw = $data['draw'];
		$search = $data['search']['value'];
		$order = $data['order'][0];
		$order_column = $data['columns'][$order['column']]['data'];
		$order_type = $order['dir'];

		$cluster_list = Cluster::join('org_source', 'org_group.source_id', '=', 'org_source.source_id')
		->select('org_group.*', 'org_source.source_name')
		->where('group_code','like',$search.'%')
		->orWhere('group_name', 'like', $search.'%')
		->orWhere('source_name', 'like', $search.'%')
		->orderBy($order_column, $order_type)
		->offset($start)->limit($length)->get();

		$cluster_list_count = Cluster::join('org_source', 'org_group.source_id', '=', 'org_source.source_id')
		->where('group_code','like',$search.'%')
		->orWhere('group_name', 'like', $search.'%')
		->orWhere('source_name', 'like', $search.'%')
		->count();

		echo json_encode(array(
				"draw" => $draw,
				"recordsTotal" => $cluster_list_count,
				"recordsFiltered" => $cluster_list_count,
				"data" => $cluster_list
		));
	}


	public function check_code(Request $request)
	{
		$cluster = Cluster::where('group_code','=',$request->group_code)->first();
		if($cluster == null){
			echo json_encode(array('status' => 'success'));
		}
		else if($cluster->group_id == $request->group_id){
			echo json_encode(array('status' => 'success'));
		}
		else {
			echo json_encode(array('status' => 'error','message' => 'Cluster code already exists'));
		}
	}



public function save(Request $request)
{
    $cluster = new Cluster();
    if ($cluster->validate($request->all()))
    {
      if($request->group_id > 0){
        $cluster = Cluster::find($request->group_id);
      }
      $cluster->fill($request->all());
      $cluster->status = 1;
      $cluster->created_by = 1;
      $result = $cluster->saveOrFail();
      echo json_encode(array('status' => 'success' , 'message' => 'Cluster details saved successfully.') );
    }
    else
    {
      // failure, get errors
      $errors = $cluster->errors();
      echo json_encode(array('status' => 'error' , 'message' => $errors));
    }
  }


  public function get(Request $request)
	{
		 $group_id = $request->group_id;
		 $cluster = Cluster::join('org_source', 'org_group.source_id', '=', 'org_source.source_id')
 			->select('org_group.*', 'org_source.source_code', 'org_source.source_name')
 			->where('org_group.group_id', '=', $group_id)->get();
 			echo json_encode($cluster);
  }


	public function change_status(Request $request)
	{
 		 	$group_id = $request->group_id;
  		$cluster = Cluster::where('group_id', $group_id)->update(['status' => 0]);
			echo json_encode(array(
	      'status' => 'success',
	      'message' => 'Cluster deactivated successfully'
	    ));
	}

	public function get_active_list(Request $request)
	{
		//$search_c = $request->search;
		$cluster_lists = Cluster::select('group_id','group_code','group_name')
		->where([['status', '=', '1']/*,['group_name', 'like', '%' . $search_c . '%'],*/])
		->get();
		return response()->json($cluster_lists);
	}

}
