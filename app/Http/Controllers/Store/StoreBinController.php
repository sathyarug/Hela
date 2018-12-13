<?php

namespace App\Http\Controllers\Store;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\Store\StoreBin;
use App\Models\Finance\Item\Category;
use Illuminate\Support\Facades\DB;

class StoreBinController extends Controller {

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
        } else if ($type == 'getBins') {
            $data = $request->all();
            return response($this->getActiveBins($data));
        } else if ($type == 'getCategory') {
            $data = $request->all();
            return response($this->getCategoryList($data));
        } else if ($type == 'getItemCategory') {
            $data = $request->all();
            return response($this->getItemCategory($data['category_id']));
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
        $storeBin = new StoreBin();
        if ($storeBin->validate($request->all())) {
            $storeBin->fill($request->all());
            $storeBin->status = 1;
            $storeBin->save();

            return response(['data' => [
                    'message' => 'Store bin saved successfully',
                    'storeBin' => $storeBin
                ]
                    ], Response::HTTP_CREATED);
        } else {
            $errors = $store->errors(); // failure, get errors
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
        $storeBin = StoreBin::find($id);
        $storeBin->store;
        $storeBin->substore;
        if ($storeBin == null)
            throw new ModelNotFoundException("Requested store not found", 1);
        else
            return response(['data' => $storeBin]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $storeBin = StoreBin::find($id);
        if ($storeBin->validate($request->all())) {
            $storeBin->fill($request->except('store_bin_name'));
            $storeBin->save();

            return response(['data' => [
                    'message' => 'Store was updated successfully',
                    'storeBin' => $storeBin
            ]]);
        } else {
            $errors = $storeBin->errors(); // failure, get errors
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
        $storeBin = StoreBin::where('store_bin_id', $id)->update(['status' => 0]);
        return response([
            'data' => [
                'message' => 'Store Bin is deactivated successfully.',
                'store' => $storeBin
            ]
                ], Response::HTTP_NO_CONTENT);
    }

    //get filtered fields only
    private function list($active = 0, $fields = null) {
        $query = null;
        if ($fields == null || $fields == '') {
            $query = StoreBin::select('*');
        } else {
            $fields = explode(',', $fields);
            $query = StoreBin::select($fields);
            if ($active != null && $active != '') {
                $query->where([['status', '=', $active]]);
            }
        }
        return $query->get();
    }

    //search goods types for autocomplete
    private function autocomplete_search($search) {
        $bin_list = StoreBin::select('store_bin_id', 'store_bin_name')
                        ->where([['store_bin_name', 'like', '%' . $search . '%'],])->get();
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

        $payload = auth()->payload();
        $locId = $payload->get('loc_id');
        //print_r($payload); exit;
        $bin_list = StoreBin::select('org_store_bin.*', 'org_substore.substore_name', 'org_store.store_name')
                        ->join('org_substore', 'org_substore.substore_id', '=', 'org_store_bin.substore_id')
                        ->join('org_store',function($join) use ($locId)
                        {
                            $join->on('org_store.store_id', '=', 'org_store_bin.store_id');
                            $join->on('org_store.loc_id', '=', DB::raw($locId) );
                        })

                        ->where([['store_bin_name', 'like', "%$search%"]])
                        ->orderBy($order_column, $order_type)
                        ->offset($start)->limit($length)->get();

        //$bin_count = StoreBin::where('store_bin_name', 'like', $search . '%')
                //->count();
        $bin_count =  StoreBin::select('org_store_bin.*', 'org_substore.substore_name', 'org_store.store_name')
                        ->join('org_substore', 'org_substore.substore_id', '=', 'org_store_bin.substore_id')
                        ->join('org_store',function($join) use ($locId)
                        {
                            $join->on('org_store.store_id', '=', 'org_store_bin.store_id');
                            $join->on('org_store.loc_id', '=', DB::raw($locId) );
                        })

                        ->where([['store_bin_name', 'like', "%$search%"]])
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
            return response($this->validate_duplicate_bin($request->id, $request->store_bin_name));
        }
    }

    //check shipment cterm code code already exists
    private function validate_duplicate_bin($id, $name) {
        $bin = StoreBin::where('store_bin_name', '=', $name)->first();
        if ($bin == null) {
            return ['status' => 'success'];
        } else if ($bin->store_bin_id == $id) {
            return ['status' => 'success'];
        } else {
            return ['status' => 'error', 'message' => 'Bin already exists'];
        }
    }

    public function getBinListByLoc(Request $request){
        $bin_list = StoreBin::select('store_bin_id', 'store_bin_name')->where('store_id', $request->id)->get();
        //$bin_list = StoreBin::select('store_bin_id','store_bin_name')->where([['store_id', '=',  $request->id],])->get();
        $bins = $bin_list->toArray();
        return response([
            'data' => $bins
        ]);
    }

    private function getActiveBins($data) {
        $bin_list = StoreBin::select('org_store_bin.*', 'org_substore.substore_name', 'org_store.store_name','org_store_bin_allocation.allocation_id')
            ->join('org_substore', 'org_substore.substore_id', '=', 'org_store_bin.substore_id')
            ->join('org_store', 'org_store.store_id', '=', 'org_store_bin.store_id')
            ->leftJoin('org_store_bin_allocation', 'org_store_bin_allocation.store_bin_id', '=', 'org_store_bin.store_bin_id')
            ->where(
            [
                ['org_store_bin.status', '=', '1'],
                ['org_substore.substore_id', '=', $data['substoreId']],
                ['org_store.store_id', '=', $data['storeId']],
            ])->get();

        $binArray= array();
        foreach($bin_list as $bin) {
            $binArray[$bin->store_bin_id] = $bin;
        }
        return [
            "data" => array_values($binArray)
        ];
    }


    private function getCategoryList() {
        return Category::select('item_category.*')
                ->where('item_category.status', '=', '1')
                ->orderBy('item_category.category_name', 'ASC')->get();
    }

    private function getItemCategory($categoryId) {
        return [
            "data" => Category::getItemListByCategory($categoryId)
        ];
    }

}
