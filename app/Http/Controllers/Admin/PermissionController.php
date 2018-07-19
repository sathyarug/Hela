<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;
//use App\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request) {
        /* $keyword = $request->get('search');
          $perPage = 25;

          if (!empty($keyword)) {
          $permission = Permission::where('name', 'LIKE', "%$keyword%")
          ->latest()->paginate($perPage);
          } else {
          $permission = Permission::latest()->paginate($perPage);
          } */

        return view('admin.permission.index', compact('permission'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create() {
        //return view('admin.permission.create');
        return view('admin.permission.edit');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request) {

        $requestData = $request->all();
        $requestData['created_by'] = 1;//Auth::id();
        
        if (Permission::create($requestData)) {
            echo json_encode(array('status' => 'success', 'message' => 'Permission saved successfully.'));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Failed saving!'));
        }

        //return redirect('admin/permission')->with('flash_message', 'Permission added!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function show($id) {
        $permission = Permission::findOrFail($id);
        return view('admin.permission.show', compact('permission'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id) {
        $permission = Permission::findOrFail($id);
        return view('admin.permission.edit', compact('permission'));
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

        $requestData = $request->all();
        $requestData['updated_by'] = 1;//Auth::id();
        
        $permission = Permission::findOrFail($id);

        if ($permission) {
            $permission->update($requestData);
            echo json_encode(array('status' => 'success', 'message' => 'Permission saved successfully.'));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Failed saving!'));
        }

        //return redirect('admin/permission')->with('flash_message', 'Permission updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id) {
        if (Permission::destroy($id)) {
            echo json_encode(array('status' => 'success', 'message' => 'Permission deleted successfully.'));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Failed deletion!'));
        }
        //return redirect('admin/permission')->with('flash_message', 'Permission deleted!');
    }

    public function getList() {
        return datatables()->of(Permission::all())->toJson();
    }

    public function checkName() {
        $id = Input::get('id');
        $name = Input::get('name');


        if ($id) {
            if (Permission::where([['name', '=', $name], ['id', '<>', $id]])->exists()) {
                echo 'true';
            } else {
                echo 'false';
            }
        } else {
            if (Permission::where('name', '=', $name)->exists()) {
                echo 'true';
            } else {
                echo 'false';
            }
        }
    }

}
