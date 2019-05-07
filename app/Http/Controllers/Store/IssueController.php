<?php

namespace App\Http\Controllers\Store;

use App\Models\Store\IssueDetails;
use App\Models\Store\IssueHeader;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class IssueController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $type = $request->type;
        $fields = $request->fields;
        $active = $request->status;
        if($type == 'datatable') {
            $data = $request->all();
            return response($this->datatable_search($data));
        }elseif($type == 'issue_details'){
            $id = $request->id;
            return response(['data' => $this->getIssueDetails($id)]);
        }else{
            $loc_id = $request->loc;
            return response(['data' => $this->list($active, $fields, $loc_id)]);
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

    public function list($active, $fields, $loc){
        $query = null;
        if($fields == null || $fields == '') {
            $query = IssueHeader::select('*');
        }else{
            $fields = explode(',', $fields);
            $query = IssueHeader::select($fields);
            if($active != null && $active != ''){
                $payload = auth()->payload();
                $query->where([['status', '=', $active], ['location', '=', $loc]]);
            }

        }
        return $query->get();
    }

    public function getIssueDetails($id){
        return IssueDetails::getIssueDetailsForReturn($id);
    }
}
