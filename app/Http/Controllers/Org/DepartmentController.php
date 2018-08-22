<?php

namespace App\Http\Controllers\Org;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Org\OrgDepartments;

class DepartmentController extends Controller
{

  public function save_dep(Request $request)
  {
    $department = new OrgDepartments();
    if ($department->validate($request->all()))
    {
      if($request->dep_id > 0){
        $department = OrgDepartments::find($request->dep_id);
      }
      $department->fill($request->all());
      $department->status = 1;
      $department->created_by = 1;
      $result = $department->saveOrFail();
           // echo json_encode(array('Saved'));
      echo json_encode(array('status' => 'success' , 'message' => 'Source details saved successfully.') );
    }
    else
    {
            // failure, get errors
      $errors = $department->errors();
      echo json_encode(array('status' => 'error' , 'message' => $errors));
    }


  }

  public function loaddata(Request $request)
  {
    $data = $request->all();
		$start = $data['start'];
		$length = $data['length'];
		$draw = $data['draw'];
		$search = $data['search']['value'];
		$order = $data['order'][0];
		$order_column = $data['columns'][$order['column']]['data'];
		$order_type = $order['dir'];

   $dep_list = OrgDepartments::select('*')
   ->where('dep_code'  , 'like', $search.'%' )
   ->orWhere('dep_name','like',$search.'%')
   ->orderBy($order_column, $order_type)
   ->offset($start)->limit($length)->get();

   $dep_count = OrgDepartments::where('dep_code'  , 'like', $search.'%' )
   ->orWhere('dep_name','like',$search.'%')
   ->count();

   echo json_encode(array(
       "draw" => $draw,
       "recordsTotal" => $dep_count,
       "recordsFiltered" => $dep_count,
       "data" => $dep_list
   ));

 }


 public function check_code(Request $request)
 {
   $department = OrgDepartments::where('dep_code','=',$request->dep_code)->first();
   if($department == null){
     echo json_encode(array('status' => 'success'));
   }
   else if($department->dep_id == $request->dep_id){
     echo json_encode(array('status' => 'success'));
   }
   else {
     echo json_encode(array('status' => 'error','message' => 'Department code already exists'));
   }
 }


public function edit(Request $request)
{
 $dep_id = $request->dep_id;
 $depedit = OrgDepartments::find($dep_id);
 echo json_encode($depedit);

}


public function delete(Request $request)
{
  $dep_id = $request->dep_id;

  $depdelete = OrgDepartments::where('dep_id', $dep_id)->update(['status' => 0]);
  echo json_encode(array(
    'status' => 'success',
    'message' => 'Department was deactivated successfully.'
  ));
}


// public function select_Source_list(Request $request){

//   $search_c = $request->search;
//   $s_source_lists = Main_Source::select('source_id','source_code','source_name')
//                     ->where([['status', '=', '1'],['source_name', 'like', '%' . $search_c . '%'],]) ->get();

//   return response()->json(['items'=>$s_source_lists]);

// }



}
