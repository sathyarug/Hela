<?php

namespace App\Http\Controllers\Merchandising;

use App\Models\Merchandising\BOMHeader;
use App\Models\Merchandising\bom_details;
use App\Models\Merchandising\CustomerOrder;
use App\Models\Merchandising\CustomerOrderDetails;
use App\Models\Merchandising\BulkCostingDetails;
use App\Models\Merchandising\BOMSOAllocation;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BomController extends Controller
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
     * @param  \App\Models\Merchandising\bom_details  $bom_details
     * @return \Illuminate\Http\Response
     */
    public function show(bom_details $bom_details)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Merchandising\bom_details  $bom_details
     * @return \Illuminate\Http\Response
     */
    public function edit(bom_details $bom_details)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Merchandising\bom_details  $bom_details
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, bom_details $bom_details)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Merchandising\bom_details  $bom_details
     * @return \Illuminate\Http\Response
     */
    public function destroy(bom_details $bom_details)
    {
        //
    }

    public function getCustOrders(Request $request){

        $customerOrder = new CustomerOrder();
        $rsCustOrderList = $customerOrder->getCustomerOrders($request->costingId);

        echo json_encode($rsCustOrderList);

    }

    public function getAssignCustOrders(Request $request){

        $customerOrder = new CustomerOrder();
        $rsCustOrderList = $customerOrder->getAssignCustomerOrders($request->costingId);

        echo json_encode($rsCustOrderList);

    }

    public function getCustomerOrderQty(Request $request){

        $customerOrderDetails = new CustomerOrderDetails();
        $rsCustomerOrderQty = $customerOrderDetails->getCustomerOrderQty($request->orderId);

        echo json_encode($rsCustomerOrderQty);
    }

    public function getCostingRMDetails(Request $request){
        $bulkCostingDetails = new BulkCostingDetails();
        $rsRMDetails = $bulkCostingDetails->getCostingItemDetails($request->costingId);        

        echo json_encode($rsRMDetails);
    }

    public function saveBOMHeader(Request $request){

        try{

            $bomHeader = new BOMHeader();
            $bomHeader->costing_id = $request->costingid;

            $bomHeader->saveOrFail();

            //Get last inserted BOM ID
            $bomID = $bomHeader->bom_id;

        }catch ( \Exception $ex) {
            
            $bomID = "fail"; 
        }        

        echo json_encode(array('bomid'=>$bomID));
    }

    public function saveBOMDetails(Request $request){

        $bomDeatils = new bom_details();
        $bomDeatils->bom_id = $request->bomid;
        $bomDeatils->item_code = $request->itemcode;
        $bomDeatils->item_color =  $request->itemcolor;
        $bomDeatils->uom_id = $request->uomid;
        $bomDeatils->unit_price = $request->unitprice;
        $bomDeatils->conpc = $request->conpc;
        $bomDeatils->total_qty = $request->totreqqty;
        $bomDeatils->total_value = $request->totvalue;
        $bomDeatils->artical_no = $request->articalno;
        $bomDeatils->status = 1;
        $bomDeatils->bal_qty = $request->totreqqty;
        $bomDeatils->item_size = $request->itemsize;

        $bomDeatils->saveOrFail();
    }

    public function saveSOAllocation(Request $request){

        try{

            $bomSOAllocation = new BOMSOAllocation();

            $bomSOAllocation->costing_id = $request->costing_id;
            $bomSOAllocation->order_id = $request->order_id;
            $bomSOAllocation->bom_id = $request->bom_id;

            $bomSOAllocation->saveOrFail();

            $status = "success";

        }catch ( \Exception $ex) {
            
            $status = "fail"; 
        }  
        echo json_encode(array('status'=>$status));
    }

    public function validateBOMExist(Request $request){



    }

    public function ListBOMS(Request $request){
        try{

            $result = BOMHeader::where("costing_id",$request->costing_id)->get();
        }catch( \Exception $ex){
            $result = "fail";
        }

        echo json_encode($result);
    }

    public function getBOMOrderQty(Request $request){

        try{

            $bomHeader = new BOMHeader();
            $result = $bomHeader->getBOMOrderQty($request->bomId);


        }catch( \Exception $ex){
            $result = $ex->getMessage();
        }

        echo json_encode($result);
    }

    public function getBOMDetails(Request $request){

        try{

            $bomDeatils = new bom_details();
            $result = $bomDeatils->GetBOMDetails($request->bomId);

        }catch ( \Exception $ex){
            $result = $ex->getMessage();
        }

        echo json_encode($result);
    }

    public function getSizeWiseDetails(Request $request){

        try{

            $customerOrderDetails = new CustomerOrderDetails();
            $result = $customerOrderDetails->getCustomerOrderSizes($request->orderId);


        }catch( \Exception $ex){
            $result = $ex->getMessage();
        }

        echo json_encode($result);
    }
}
