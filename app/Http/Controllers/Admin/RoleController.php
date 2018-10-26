<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller {
    
    
      public function __construct() {
        //add functions names to 'except' paramert to skip authentication
        $this->middleware('jwt.verify', ['except' => ['index']]);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
     public function index(Request $request) 
     {
    
        /* $keyword = $request->get('search');
          $perPage = '';

          if (!empty($keyword)) {
          $role = Role::where('name', 'LIKE', "%$keyword%")
          ->latest()->paginate($perPage);
          } else {
          $role = Role::latest()->paginate($perPage);
          }
          return view('admin.role.index', compact('role'));
         */
        // return view('admin.role.index');

        /*$permissions = Permission::get()->pluck('name', 'name');
        return view('admin.role.index', compact('permissions'));*/
        $type = $request->type;
      
      if($type == 'datatable')   {
        $data = $request->all();
        return response($this->datatable_search($data));
      }
      else if($type == 'auto')    {
        $search = $request->search;
        return response($this->autocomplete_search($search));
      }
     /* else if($type == 'getPermissionList') {
          return response([
            'data' => Permission::get()->pluck('name', 'name')->toArray()
           ]);
      }*/
      else {
        $active = $request->active;
        $fields = $request->fields;
        return response([
          'data' => $this->list($active , $fields)
        ]);
      }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create() {
        $permissions = Permission::get()->pluck('name', 'name');
        return view('admin.role.create', compact('permissions'));
        //return view('admin.role.form', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request) {

        $requestData = $request->except('permissions');
        $permissions = $request->permissions;

        $requestData['created_by'] = Auth::id();
        $role = Role::create($requestData);
        
        $permissions = array_map('current', $permissions);
        //print_r($permissions); exit;
        if ($role) {
            $role->givePermissionTo($permissions);
        } else {
            return response(['errors' => ['validationErrors' => 'Failed saving!' ]], Response::HTTP_UNPROCESSABLE_ENTITY);
            //echo json_encode(array('status' => 'error', 'message' => 'Failed saving!'));
        }

        return response([ 'data' => [
          'message' => 'Permission saved successfully.'
          ]
        ], Response::HTTP_CREATED );
        //echo json_encode(array('status' => 'success', 'message' => 'Role saved successfully.'));

        // return redirect('admin/role')->with('flash_message', 'Role added!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function show($id) {
        $role = Role::findOrFail($id);
        $permissions = $role->permissions->pluck('name');
        
        if($role == null)
          throw new ModelNotFoundException("Requested permission not found", 1);
        else
          return response([ 'data' => $role , 'permissions'=> $permissions ]);
        //return view('admin.role.show', compact('role', 'permissions'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id) {
        $role = Role::findOrFail($id);
        $permissions = Permission::get()->pluck('name', 'name');
        //return view('admin.role.form', compact('role', 'permissions'));
        return view('admin.role.edit', compact('role', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id) {

        $requestData = $request->except('permissions');
        $permissions = $request->permissions;
        $requestData['updated_by'] = Auth::id();

        $role = Role::findOrFail($id);
        if ($role) {
            $role->update($requestData);
            $role->syncPermissions($permissions);
             return response([ 'data' => [
            'message' => 'Role is updated successfully',
            'role' => $role
          ]]);
           // echo json_encode(array('status' => 'success', 'message' => 'Role saved successfully.'));
        } else {
           // echo json_encode(array('status' => 'error', 'message' => 'Failed saving!'));
        }


        //return redirect('admin/role')->with('flash_message', 'Role updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id) {
        
        if (Role::destroy($id)) {
             return response([
            'data' => [
              'message' => 'Permission deleted successfully.'
            ]
          ] , Response::HTTP_NO_CONTENT);
        }
             
       /* if( Role::destroy($id) ) {
           echo json_encode(array('status' => 'success', 'message' => 'Role deleted successfully.'));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Failed deletion!'));
        }*/

       // return redirect('admin/role')->with('flash_message', 'Role deleted!');
    }

    public function getList() {
        return datatables()->of(Role::all())->toJson();
        //$role = Role::all()->toBase();
        //echo json_encode($role);
    }

    public function checkName() {

        if (Role::where('name', '=', Input::get('name') )->exists()) {
                echo 'true';
            } else {
                echo 'false';
            }
            
        /*$id = Input::get('id');
        $name = Input::get('name');


        if ($id > 0) {
            $count = Role::where([
                            ['id', '!=', $id],
                            ['name', '=', $name],
                    ])->count();
            if ($count == 1) {
                echo 'true';
            } else {
                echo 'false';
            }
        } else {
            if (Role::where('name', '=', $name)->exists()) {
                echo 'true';
            } else {
                echo 'false';
            }
        }*/
    }
    
    
      //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
      $query = null;
      if($fields == null || $fields == '') {
        $query = Role::select('*');
      }
      else{
        $fields = explode(',', $fields);
        $query = Role::select($fields);
        /*if($active != null && $active != ''){
          $query->where([['status', '=', $active]]);
        }*/
      }
      return $query->get();
    }


    //search goods types for autocomplete
    private function autocomplete_search($search)
  	{
  		$role_list = Role::select('id','name')
  		->where([['name', 'like', '%' . $search . '%'],]) ->get();
  		return $role_list;
  	}


    //get searched goods types for datatable plugin format
    private function datatable_search($data)
    {
      $start = $data['start'];
      $length = $data['length'];
      $draw = $data['draw'];
      $search = $data['search']['value'];
      $order = $data['order'][0];
      $order_column = $data['columns'][$order['column']]['data'];
      $order_type = $order['dir'];
      
      $role_list = Role::select('*')
      ->where('name'  , 'like', $search.'%' )
      ->orderBy($order_column, $order_type)
      ->offset($start)->limit($length)->get();

      $role_count = Role::where('name'  , 'like', $search.'%' )
      ->count();

      return [
          "draw" => $draw,
          "recordsTotal" => $role_count,
          "recordsFiltered" => $role_count,
          "data" => $role_list
      ];
    }
    
    //validate anything based on requirements
    public function validate_data(Request $request){
      $for = $request->for;
      if($for == 'duplicate')
      {
        return response($this->validate_duplicate_role($request->id , $request->name));
      }
    }


    //check shipment cterm code code already exists
    private function validate_duplicate_role($id , $name)
    {
      $role = Role::where('name','=',$name)->first();
      if($role == null){
        return ['status' => 'success'];
      }
      else if($role->id == $id){
        return ['status' => 'success'];
      }
      else {
        return ['status' => 'error','message' => 'Role already exists'];
      }
    }
    

}
