<?php

namespace App\Http\Controllers\Store;

use App\Models\Store\IssueDetails;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
        //

        foreach ($request->return_data as $returnData){
            if($returnData['item_select'] == true){
                //Article::with('category')->whereIn('id', $ids)->get();
                //dd($returnData);

                //$mod = IssueDetails::with('issue')->whereIn('id', $returnData['issue_line_id'])->get();
                $mod = IssueDetails::find($returnData['issue_line_id']);
                dd($mod);
            }
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
