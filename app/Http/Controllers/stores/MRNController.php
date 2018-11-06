<?php

namespace App\Http\Controllers\Org\Location;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use App\Models\Org\Location\Cluster;

class ClusterController extends Controller
{
    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
    }

    //get Cluster list
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
      else {
        $active = $request->active;
        $fields = $request->fields;
        return response([
          'data' => $this->list($active , $fields)
        ]);
      }
    }


    //create a Cluster
    public function store(Request $request)
    {
      $cluster = new Cluster();
      if($cluster->validate($request->all()))
      {
        $cluster->fill($request->all());
        $cluster->status = 1;
        $cluster->save();

        return response([ 'data' => [
          'message' => 'Cluster was saved successfully',
          'cluster' => $cluster
          ]
        ], Response::HTTP_CREATED );
      }
      else
      {
          $errors = $cluster->errors();// failure, get errors
          return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }


    //get a Cluster
    public function show($id)
    {
      $cluster = Cluster::find($id);
      if($cluster == null)
        throw new ModelNotFoundException("Requested cluster not found", 1);
      else
        return response([ 'data' => $cluster ]);
    }


    //update a Cluster
    public function update(Request $request, $id)
    {
      $cluster = Cluster::find($id);
      if($cluster->validate($request->all()))
      {
        $cluster->fill($request->except('group_code'));
        $cluster->save();

        return response([ 'data' => [
          'message' => 'Cluster was updated successfully',
          'cluster' => $cluster
        ]]);
      }
      else
      {
        $errors = $cluster->errors();// failure, get errors
        return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }


    //deactivate a Cluster
    public function destroy($id)
    {
      $cluster = Cluster::where('group_id', $id)->update(['status' => 0]);
      return response([
        'data' => [
          'message' => 'Cluster was deactivated successfully.',
          'cluster' => $cluster
        ]
      ] , Response::HTTP_NO_CONTENT);
    }


    //validate anything based on requirements
    public function validate_data(Request $request){
      $for = $request->for;
      if($for == 'duplicate')
      {
        return response($this->validate_duplicate_code($request->group_id , $request->group_code));
      }
    }


    //check Cluster code already exists
    private function validate_duplicate_code($id , $code)
    {
      $cluster = Cluster::where('group_code','=',$code)->first();
      if($cluster == null){
        return ['status' => 'success'];
      }
      else if($cluster->group_id == $id){
        return ['status' => 'success'];
      }
      else {
        return ['status' => 'error','message' => 'Cluster code already exists'];
      }
    }


    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
      $query = null;
      if($fields == null || $fields == '') {
        $query = Cluster::select('*');
      }
      else{
        $fields = explode(',', $fields);
        $query = Cluster::select($fields);
        if($active != null && $active != ''){
          $query->where([['status', '=', $active]]);
        }
      }
      return $query->get();
    }

    //search Cluster for autocomplete
    private function autocomplete_search($search)
  	{
  		$cluster_lists = Cluster::select('group_id','group_name')
  		->where([['group_name', 'like', '%' . $search . '%'],]) ->get();
  		return $cluster_lists;
  	}

      //
    //get searched Clusters for datatable plugin format
    private function datatable_search($data)
    {
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

  		$cluster_count = Cluster::join('org_source', 'org_group.source_id', '=', 'org_source.source_id')
  		->where('group_code','like',$search.'%')
  		->orWhere('group_name', 'like', $search.'%')
  		->orWhere('source_name', 'like', $search.'%')
  		->count();

      return [
          "draw" => $draw,
          "recordsTotal" => $cluster_count,
          "recordsFiltered" => $cluster_count,
          "data" => $cluster_list
      ];
    }

}
