<?php

namespace App\Http\Controllers\Store;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\Store\SubStore;

class SubStoreController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $type = $request->type;

        if ($type == 'datatable') {
            $data = $request->all();
            return response($this->datatable_search($data));
        } else if ($type == 'auto') {
            $search = $request->search;
            return response($this->autocomplete_search($search));
        } else {
            $active = $request->active;
            $fields = $request->fields;
            return response([
                'data' => $this->list($active, $fields)
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $subStore = new SubStore();
        if ($subStore->validate($request->all())) {
            $subStore->fill($request->all());
            $subStore->status = 1;
            $subStore->save();

            return response(['data' => [
                    'message' => 'Sub Store saved successfully',
                    'subStore' => $subStore
                ]
                    ], Response::HTTP_CREATED);
        } else {
            $errors = $subStore->errors(); // failure, get errors
            return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $subStore = SubStore::find($id);
        if ($subStore == null)
            throw new ModelNotFoundException("Requested sub store not found", 1);
        else
            return response(['data' => $subStore]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $subStore = SubStore::find($id);
        if ($subStore->validate($request->all())) {
            $subStore->fill($request->except('substore_name'));
            $subStore->save();

            return response(['data' => [
                    'message' => 'SubStore is updated successfully',
                    'subStore' => $subStore
            ]]);
        } else {
            $errors = $subStore->errors(); // failure, get errors
            return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $subStore = SubStore::where('substore_id', $id)->update(['status' => 0]);
        return response([
            'data' => [
                'message' => 'Sub Store is deactivated successfully.',
                'store' => $subStore
            ]
                ], Response::HTTP_NO_CONTENT);
    }

    //get filtered fields only
    private function list($active = 0, $fields = null) {
        $query = null;
        if ($fields == null || $fields == '') {
            $query = SubStore::select('*');
        } else {
            $fields = explode(',', $fields);
            $query = SubStore::select($fields);
            if ($active != null && $active != '') {
                $query->where([['status', '=', $active]]);
            }
        }
        return $query->get();
    }

    //search goods types for autocomplete
    private function autocomplete_search($search) {
        $bin_list = SubStore::select('substore_id', 'substore_name')
                        ->where([['substore_name', 'like', '%' . $search . '%'],])->get();
        return $bin_list;
    }

    //get searched goods types for datatable plugin format
    private function datatable_search($data) {
        $start = $data['start'];
        $length = $data['length'];
        $draw = $data['draw'];
        $search = $data['search']['value'];
        $order = $data['order'][0];
        $order_column = $data['columns'][$order['column']]['data'];
        $order_type = $order['dir'];

        $bin_list = SubStore::select('*')
                        ->where('substore_name', 'like', $search . '%')
                        ->orderBy($order_column, $order_type)
                        ->offset($start)->limit($length)->get();

        $bin_count = SubStore::where('substore_name', 'like', $search . '%')
                ->count();

        return [
            "draw" => $draw,
            "recordsTotal" => $bin_count,
            "recordsFiltered" => $bin_count,
            "data" => $bin_list
        ];
    }

    //validate anything based on requirements
    public function validate_data(Request $request) {
        $for = $request->for;
        if ($for == 'duplicate') {
            return response($this->validate_duplicate_substore($request->substore_id, $request->substore_name));
        }
    }

    //check shipment cterm code code already exists
    private function validate_duplicate_substore($id, $name) {
        $bin = SubStore::where('substore_name', '=', $name)->first();
        if ($bin == null) {
            return ['status' => 'success'];
        } else if ($bin->substore_id == $id) {
            return ['status' => 'success'];
        } else {
            return ['status' => 'error', 'message' => 'Sub store already exists'];
        }
    }
}
