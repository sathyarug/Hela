<?php

namespace App\Http\Controllers\Org;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use App\Models\Org\Department;
use App\Libraries\AppAuthorize;
use Exception;

class DepartmentController extends Controller
{
    var $authorize = null;

    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
      $this->authorize = new AppAuthorize();
    }

    //get Department list
    public function index(Request $request)
    {
      $type = $request->type;
      if($type == 'datatable')   {
        $data = $request->all();
        $this->datatable_search($data);
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


    //create a Department
    public function store(Request $request)
    {
      if($this->authorize->hasPermission('DEPARTMENT_MANAGE'))//check permission
      {
        $department = new Department();
        if($department->validate($request->all()))
        {
          $department->fill($request->all());
          $department->status = 1;
          $department->save();

          return response([ 'data' => [
            'message' => 'Department saved successfully',
            'department' => $department
            ]
          ], Response::HTTP_CREATED );
        }
        else
        {
            $errors = $department->errors();// failure, get errors
            return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
      }
      else {
        return response($this->authorize->error_response(), 401);
      }
    }


    //get a Department
    public function show($id)
    {
      // $error = 'Always throw this error';
      //  throw new Exception($error);
      if($this->authorize->hasPermission('DEPARTMENT_MANAGE'))//check permission
      {
        $department = Department::find($id);
        if($department == null)
          throw new ModelNotFoundException("Requested department not found", 1);
        else
          return response([ 'data' => $department ]);
      }
      else {
        return response($this->authorize->error_response(), 401);
      }
    }


    //update a Department
    public function update(Request $request, $id)
    {
      if($this->authorize->hasPermission('DEPARTMENT_MANAGE'))//check permission
      {
        $department = Department::find($id);
        if($department->validate($request->all()))
        {
          $department->fill($request->except('dep_code'));
          $department->save();

          return response([ 'data' => [
            'message' => 'Department updated successfully',
            'department' => $department
          ]]);
        }
        else
        {
          $errors = $department->errors();// failure, get errors
          return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
      }
      else {
        return response($this->authorize->error_response(), 401);
      }
    }


    //deactivate a Department
    public function destroy($id)
    {
      if($this->authorize->hasPermission('DEPARTMENT_DELETE'))//check permission
      {
        $department = Department::where('dep_id', $id)->update(['status' => 0]);
        return response([
          'data' => [
            'message' => 'Department was deactivated successfully.',
            'department' => $department
          ]
        ] , Response::HTTP_NO_CONTENT);
      }
      else {
        return response($this->authorize->error_response(), 401);
      }
    }


    //validate anything based on requirements
    public function validate_data(Request $request){
      $for = $request->for;
      if($for == 'duplicate')
      {
        return response($this->validate_duplicate_code($request->dep_id , $request->dep_code));
      }
    }


    //check Department code already exists
    private function validate_duplicate_code($id , $code)
    {
      $department = Department::where('dep_code','=',$code)->first();
      if($department == null){
        return ['status' => 'success'];
      }
      else if($department->dep_id == $id){
        return ['status' => 'success'];
      }
      else {
        return ['status' => 'error','message' => 'Department code already exists'];
      }
    }


    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
      $query = null;
      if($fields == null || $fields == '') {
        $query = Department::select('*');
      }
      else{
        $fields = explode(',', $fields);
        $query = Department::select($fields);
        if($active != null && $active != ''){
          $query->where([['status', '=', $active]]);
        }
      }
      return $query->get();
    }

    //search Department for autocomplete
    private function autocomplete_search($search)
  	{
  		$department_lists = Department::select('dep_id','dep_name')
  		->where([['dep_name', 'like', '%' . $search . '%'],['status','<>','0']]) ->get();
  		return $department_lists;
  	}


    //get searched Departments for datatable plugin format
    private function datatable_search($data)
    {
      if($this->authorize->hasPermission('DEPARTMENT_MANAGE'))//check permission
      {
        $start = $data['start'];
        $length = $data['length'];
        $draw = $data['draw'];
        $search = $data['search']['value'];
        $order = $data['order'][0];
        $order_column = $data['columns'][$order['column']]['data'];
        $order_type = $order['dir'];

        $dep_list = Department::select('*')
        ->where('dep_code'  , 'like', $search.'%' )
        ->orWhere('dep_name','like',$search.'%')
        ->orderBy($order_column, $order_type)
        ->offset($start)->limit($length)->get();

        $dep_count = Department::where('dep_code'  , 'like', $search.'%' )
        ->orWhere('dep_name','like',$search.'%')
        ->count();

        echo json_encode([
            "draw" => $draw,
            "recordsTotal" => $dep_count,
            "recordsFiltered" => $dep_count,
            "data" => $dep_list
        ]);
      }
      else {
        return response($this->authorize->error_response(), 401);
      }
    }

}
