<?php

namespace App\Http\Controllers\Merchandising\Costing\Flash;

use App\Models\Merchandising\Costing\Flash\cost_flash_header;
use App\Models\Merchandising\Costing\Flash\cost_flash_details;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FlashController extends Controller
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
     * @param  \App\Models\Merchandising\Costing\Flash\cost_flash_header  $cost_flash_header
     * @return \Illuminate\Http\Response
     */
    public function show(cost_flash_header $cost_flash_header)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Merchandising\Costing\Flash\cost_flash_header  $cost_flash_header
     * @return \Illuminate\Http\Response
     */
    public function edit(cost_flash_header $cost_flash_header)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Merchandising\Costing\Flash\cost_flash_header  $cost_flash_header
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, cost_flash_header $cost_flash_header)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Merchandising\Costing\Flash\cost_flash_header  $cost_flash_header
     * @return \Illuminate\Http\Response
     */
    public function destroy(cost_flash_header $cost_flash_header)
    {
        //
    }
    
    public function saveCostingHeader(Request $request){
        
        try{
            
            if(cost_flash_header::where("costing_id",$request->costing_id)->exists()){
                
                $costHeaderUpdate = cost_flash_header::where("costing_id",$request->costing_id)->first();
                $costHeaderUpdate->update(["order_qty"=>$request->order_qty, "season_id"=>$request->season_id, "order_smv"=>$request->sewing_smv, 
                                           "order_fob"=>$request->order_fob, "order_eff"=>$request->order_eff,"packing_smv"=>$request->packing_smv, 
                                           "labour_sub_cost"=>$request->labour_cost, "finance_cost" => $request->finance_cost, "corporate_cost" =>$request->corporate_cost,
                                           "epm_rate"=>$request->epm, "netprofit"=>$request->np, "factory_cpm"=>$request->fac_cpm, "frontend_cpm"=>$request->front_cpm, "finance_rate"=>$request->fin_rate]);
            }else{
                
                $costingHeader = new cost_flash_header();        
                $costingHeader->style_id = $request->style_code;        
                $costingHeader->order_qty = $request->order_qty;        
                $costingHeader->season_id = $request->season_id;        
                $costingHeader->order_smv = $request->sewing_smv;        
                $costingHeader->order_fob = $request->order_fob;        
                $costingHeader->order_eff = $request->order_eff;        
                $costingHeader->sewing_smv = $request->sewing_smv;        
                $costingHeader->packing_smv = $request->packing_smv; 
                $costingHeader->labour_sub_cost = $request->labour_cost; 
                $costingHeader->finance_cost = $request->finance_cost; 
                $costingHeader->corporate_cost = $request->corporate_cost; 
                $costingHeader->epm_rate = $request->epm; 
                $costingHeader->netprofit = $request->np; 
                $costingHeader->factory_cpm = $request->fac_cpm; 
                $costingHeader->frontend_cpm = $request->front_cpm; 
                $costingHeader->finance_rate = $request->fin_rate; 
                $costingHeader->approval_no = 0;             
                $costingHeader->order_status = 0;

                $costingHeader->saveOrFail();
                
            }
            
            
            
            //$status = $costingHeader->costing_id; //"success";
            $status = $request->costing_id; //"success";
            
            
        } catch ( \Exception $ex) {
            $status = "fail";    
        }
        echo json_encode(array('status' => $status));          
    }
    
    public function saveCostingDetails(Request $request){
        
        try{
            
            if(cost_flash_details::where("costing_id",$request->costing_id)->where("master_id",$request->item_code)->exists()){
                
                $costDetailsUpdate = cost_flash_details::where("costing_id",$request->costing_id)->where("master_id",$request->item_code)->first();
                $costDetailsUpdate->update(["uom_id"=>$request->uom_id, "conpc"=>$request->con_pc, "unitprice"=>$request->unit_price, "wastage"=>$request->wastage,
                                            "total_required_qty"=>$request->total_required_qty, "total_value"=>$request->total_value, "supplier_id"=>$request->supplier_id]);
                
            }else{
                
                $costingDetails = new cost_flash_details();
                $costingDetails->costing_id = $request->costing_id;
                $costingDetails->style_id = $request->style_code;
                $costingDetails->master_id = $request->item_code;
                $costingDetails->uom_id = $request->uom_id;
                $costingDetails->conpc = $request->con_pc;
                $costingDetails->unitprice = $request->unit_price;
                $costingDetails->wastage = $request->wastage;
                $costingDetails->total_required_qty = $request->total_required_qty;
                $costingDetails->total_value = $request->total_value;
                $costingDetails->supplier_id = $request->supplier_id;
                $costingDetails->saveOrFail();
                
            }
            
            
            
             $status = "success";
            
            
        } catch ( \Exception $ex) {
            
            $status = "fail"; 

        }
        echo json_encode(array('status' => $status));        
    }
    
    public function confirmCostSheet(Request $request){
        
        try{
            
            $costHeaderConfirm = cost_flash_header::where("costing_id",$request->costing_id)->first();
            $costHeaderConfirm->update(["order_status"=>3, "confirm_at" => date("Y-m-d")]);
            
            //DB::table('cost_flash_header')->where()->update(["confirm_at" => date("Y-m-d")]); 
            //echo $request->costing_id;
            
            $status = "success";
            
        } catch ( \Exception $ex) {
            $status = "fail";
        }
        
        echo json_encode(array('status' => $status)); 
        
    }
    
    public function reviseCostSheet(Request $request){
        
        try{
            
            $costHeaderConfirm = cost_flash_header::where("costing_id",$request->costing_id)->first();
            $costHeaderConfirm->update(["order_status"=>0, "revised_on" => date("Y-m-d")]);
            
            $status = "success";
            
        } catch ( \Exception $ex) {
            $status = "fail";
        }
        
        echo json_encode(array('status' => $status)); 
        
    }
    
    public function listingCostings(Request $request){
        
        //$costListings = cost_flash_header::select("costing_id", "LPAD(costing_id,6,0)")->get();
        $costHeader = new cost_flash_header();
        $costListings = $costHeader->ListCostingId($request->style_id);        
        
        echo json_encode($costListings);
        
    }
    
    public function getCostingHeader(Request $request){
        
        //$costingHeaderDetails = cost_flash_header::find($request->costing_id);
        $costingHeaderDetails = cost_flash_header::where('costing_id','=',$request->costing_id)->get();
        echo json_encode($costingHeaderDetails);
    }
    
    public function getCostingLines(Request $request){
        
        //$costingLineDetails = cost_flash_details::where('costing_id','=',$request->costing_id)->get();
        $costingLineDetails = new cost_flash_details();
        $rsCostingDetails = $costingLineDetails->getCostingLineDetails($request->costing_id);
        echo json_encode($rsCostingDetails);
    }
}
