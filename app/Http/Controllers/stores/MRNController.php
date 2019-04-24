<?php

namespace App\Http\Controllers\Org\Location;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use App\Models\Org\Location\Cluster;

class MRNController extends Controller
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

    }


    //get a Cluster
    public function show($id)
    {

    }


    //update a Cluster
    public function update(Request $request, $id)
    {

    }


    //deactivate a Cluster
    public function destroy($id)
    {

    }


    //validate anything based on requirements
    public function validate_data(Request $request){

    }




    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
      $query = null;
      if($fields == null || $fields == '') {
        $query = Mrn::select('*');
      }
      else{
        $fields = explode(',', $fields);
        $query = MRNHeader::select($fields);
        if($active != null && $active != ''){
          $query->where([['status', '=', $active]]);
        }
      }
      return $query->get();
    }




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

    public function searchStock(Request $request){
        print_r($request->style_no);
    }

}
