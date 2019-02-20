<?php

namespace App\Http\Controllers\Merchandising;

use App\Models\Merchandising\PoOrderDetails;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Merchandising\PoOrderHeader;

use Illuminate\Support\Facades\DB;

class PurchaseOrder extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
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
        //
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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

    public function loadPoLineData(Request $request){
        $podata = PoOrderHeader::getPoLineData($request);
        return response([
            'data' => $podata
        ]);
        //return response()->json(true);
    }

    public function loadPoBpoList(Request $request){
        $po = PoOrderHeader::select('po_id', 'po_number')->where('po_id', $request->id)->get();
        $podata = $po->toArray();

        return response([
            'data' => $podata
        ]);
    }

    public function getPoSCList(Request $request){
        $po = PoOrderDetails::select('sc_no')->where('po_id', $request->id)->get();
        $podata = $po->toArray();

        return response([
            'data' => $podata
        ]);
    }

    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
        $query = null;
        if($fields == null || $fields == '') {
            $query = PoOrderHeader::select('*');
        }
        else{
            $fields = explode(',', $fields);
            $query = PoOrderHeader::select($fields);
            if($active != null && $active != ''){
                $query->where([['status', '=', $active]]);
            }
        }
        return $query->get();
    }
}
