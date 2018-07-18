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
      if($request->department_hid > 0){
        $department = OrgDepartments::find($request->department_hid);
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

  public function loaddata()
  {
   $dep_list = OrgDepartments::all();
   echo json_encode($dep_list);

 }

 public function check_department(Request $request)
 {


  $count = OrgDepartments::where('dep_code','=',$request->code)->count();

  if($request->idcode > 0){

    $user = OrgDepartments::where('dep_id', $request->idcode)->first();
   //print_r($user);
    if($user->dep_code == $request->code)
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
  echo json_encode(array('delete'));
}


// public function select_Source_list(Request $request){

//   $search_c = $request->search;
//   $s_source_lists = Main_Source::select('source_id','source_code','source_name')
//                     ->where([['status', '=', '1'],['source_name', 'like', '%' . $search_c . '%'],]) ->get();

//   return response()->json(['items'=>$s_source_lists]);

// }



}
