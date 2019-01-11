<?php

namespace App\Http\Controllers\Store;

use App\Models\Store\BinTransfer;
use App\Models\Store\Stock;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Store\BinTransferDetail;

class BinTransferController extends Controller
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
        dd($request);
       /* $header = new BinTransfer;
        $header->sub_store = $request->sub_store;
        $header->created_by = 58814;
        $header->save();*/

        foreach($request->binData as $bin){
            dd($bin);
            $modal = new BinTransferDetail;
            $modal->sub_store = 100;
           // $modal->transfer_id = $header->id;
            $modal->from_bin = 100;
            $modal->to_bin = $request->bin;
            $modal->style = $bin->style;
            $modal->color = $bin->colour;
            $modal->qty = $bin['qty'];
            $modal->save();
        }



    }

    public function addBinTrnsfer(Request $request){
         $header = new BinTransfer;
         $header->sub_store = $request->sub_store;
         $header->created_by = 58814;
         $header->save();

        foreach($request->binModalData as $bin){
            $modal = new BinTransferDetail;
            $modal->sub_store = 100;
            $modal->transfer_id = $header->id;
            $modal->from_bin = 100;
            $modal->to_bin = 1;
            $modal->style = $bin['style_id'];
            $modal->color = $bin['colour'];
            $modal->qty = $bin['qty'];
            $modal->color = $bin['color_id'];
            $modal->size = $bin['size_id'];
            $modal->uom = $bin['uom_id'];
            $modal->material_id = $bin['material_id'];
            $modal->save();
        }

        return $header->id;
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

    public function loadBinQty(){
       $stock = Stock::getBinStock(1);

        return response([
            'data' => $stock
        ]);
    }

    public function loadAddedBinQty(Request $request){
        $stock = BinTransfer::getAddedBinStock($request->id);
        //$stock = Stock::getBinStock(1);

        return response([
            'data' => $stock
        ]);
    }




}
