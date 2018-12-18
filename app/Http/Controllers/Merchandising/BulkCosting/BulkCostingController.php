<?php

namespace App\Http\Controllers\Merchandising\BulkCosting;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Merchandising\BulkCosting;

class BulkCostingController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $type = $request->type;

        if ($type == 'getSeasonList') {
            return response($this->getSeasonList());
        } elseif ($type == 'getColorType') {
            return response($this->getColorType());
        } elseif ($type == 'auto') {
            $search = $request->search;
            return response($this->getStyleList($search));
        } elseif ($type == 'getStyleData') {
            return response($this->getStyleData($request->style_id));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        echo 'Create';
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $model = new BulkCosting();
        if ($model->validate($request->all())) {
            $model->fill($request->all());
            $model->status = 1;
            
            $payload = auth()->payload();
            $model->user_loc_id = $payload->get('loc_id');
            
            $model->save();

            return response(['data' => [
                    'message' => 'Costing is saved successfully',
                    'bulkCostin' => $model
                ]
                    ], Response::HTTP_CREATED);
        } else {
            $errors = $model->errors(); // failure, get errors
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
        echo 'Show';
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        echo 'Edit';
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        echo 'Update';
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        echo 'Destroy';
    }

    private function getSeasonList() {
        //return \App\Models\Org\Customer::getActiveCustomerList();
        return \App\Models\Org\Season::select('season_id', 'season_name')
                        ->where([['status', '=', 1]])->get();
    }

    private function getColorType() {
        return \App\Models\Merchandising\ColorOption::select('col_opt_id', 'color_option')
                        ->where([['status', '=', 1]])->get();
    }

    private function getStyleList($search) {
        return \App\Models\Merchandising\styleCreation::select('style_id', 'style_no')
                        ->where([['style_no', 'like', '%' . $search . '%'],])->get();
    }

    private function getStyleData($style_id) {
        $dataArr = array();
        //$styleData = \App\Models\Merchandising\styleCreation::select('*')
        //     ->where([['style_id', '=', $style_id ],]) ->get();

        $styleData = \App\Models\Merchandising\styleCreation::find($style_id);
        $country = \App\Models\Org\Country::find($styleData->customer->customer_country);
        //dd($styleData->customer);
        // $styleData->customer_id;    $styleData->division_id pack_type_id
        $dataArr['style_remark'] = $styleData->remark;
        $dataArr['style_desc'] = $styleData->style_description;
        $dataArr['style_id'] = $styleData->style_id;
        $dataArr['style_no'] = $styleData->style_no;
        $dataArr['cust_name'] = $styleData->customer->customer_name;
        $dataArr['cust_id'] = $styleData->customer->customer_id;
        $dataArr['division_name'] = $styleData->division->division_description;
        $dataArr['division_id'] = $styleData->division->division_id;
        $dataArr['country'] = $country->country_description;
        $dataArr['stage'] = 'Bulk Costing';
        return $dataArr;
    }

}
