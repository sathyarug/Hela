<?php

namespace App\Http\Controllers\Store;

use App\Models\Store\IssueDetails;
use App\Models\Store\ReturnToStoresDetails;
use App\Models\Store\ReturnToStoresHeader;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Libraries\UniqueIdGenerator;

class ReturnToStoresController extends Controller
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

        //Saving to Return to stores tables
        $saved = false;
        $rth = new ReturnToStoresHeader;
        if($rth->validate($request->all())){
            $rtID = UniqueIdGenerator::generateUniqueId('RETURN_TO_STORES', auth()->payload()['loc_id']);
            $rth->return_no = $rtID;
            $rth->location = auth()->payload()['loc_id'];
            $rth->stores = auth()->payload()['loc_id'];
            $rth->created_by = auth()->payload()['user_id'];
            $rth->save();
        }

        foreach ($request->return_data as $returnData){
            if($returnData['item_select'] == true && $returnData['qty'] > 0){
                //Saving to Return to stores details
                $issueData = IssueDetails::find($returnData['issue_line_id']);

                $rtd = new ReturnToStoresDetails();
                $rtd->return_id = $rth->return_id;
                $rtd->so_no = $issueData->so_no;
                $rtd->cus_po = $issueData->cus_po;
                $rtd->item_code = $issueData->item_code;
                $rtd->material_code = $issueData->material_code;
                $rtd->color = $issueData->color;
                $rtd->size = $issueData->size;
                $rtd->uom = $issueData->uom;
                $rtd->grn_no = $issueData->grn_no;
                $rtd->issue_id = $issueData->issue_id;
                $rtd->qty = $returnData['qty'];

                if($rtd->save()){
                    $saved = true;
                }
            }
        }
        if($saved){
            return response([ 'data' => [
                'message' => 'Saved Successfully'
            ]
            ], Response::HTTP_CREATED );
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
