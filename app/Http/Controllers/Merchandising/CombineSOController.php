<?php

namespace App\Http\Controllers\Merchandising;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Merchandising\CostingSOCombine;

class CombineSOController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        foreach ($request->soList as $item) {
            $modal = new CostingSOCombine;
            $modal->costing_id = 1;
            $modal->color = $item['color_id'];
            $modal->details_id = $item['details_id'];
            $modal->qty = $item['qty'];
            $modal->comb_id = 1;
            $modal->created_by = 58814;
            $modal->save();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
