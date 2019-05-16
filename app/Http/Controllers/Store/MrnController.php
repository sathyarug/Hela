<?php

namespace App\Http\Controllers\Store;

use App\Models\Store\MRNHeader;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Org\Location\Cluster;
use App\Models\mrn\MRN;

class MrnController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $type = $request['type'];
        if($type == 'datatable') {
            $draw = $request['draw'];
            $po = $request['text'];
            return $this->dataTable($draw, $po);
        }elseif($type == 'load-mrn'){
            $mrnId = $request['mrn'];
            $locId = $request['loc'];
            return $this->loadMrn($mrnId, $locId);

        }elseif ($type == 'mrn-select'){
            $soId = $request['so'];
            $active = $request->active;
            $fields = $request->fields;

            return $this->loadMrnList($soId, $fields);
        }

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

    public function loadMrnList($soId, $fields){

        $mrnList = MRNHeader::getMRNList($soId);

        return response([
            'data' => $mrnList
        ]);

    }

    public function loadMrn($mrnId, $locId){
       // $mrn = MRNHeader::
    }
}
