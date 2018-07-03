<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    // public function index(Request $request)
    public function index() {
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

        $permissions = Permission::get()->pluck('name', 'name');
        return view('admin.role.index', compact('permissions'));
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
        if ($role) {
            $role->givePermissionTo($permissions);
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Failed saving!'));
        }

        echo json_encode(array('status' => 'success', 'message' => 'Role saved successfully.'));

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
        return view('admin.role.show', compact('role', 'permissions'));
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
            echo json_encode(array('status' => 'success', 'message' => 'Role saved successfully.'));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Failed saving!'));
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
        if( Role::destroy($id) ) {
           echo json_encode(array('status' => 'success', 'message' => 'Role deleted successfully.'));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Failed deletion!'));
        }

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

}
