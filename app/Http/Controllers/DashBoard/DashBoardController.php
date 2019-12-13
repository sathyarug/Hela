<?php

namespace App\Http\Controllers\DashBoard;

use App\Models\Merchandising\StyleCreation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Merchandising\CustomerOrder;
use App\Models\Org\Customer;
use App\Models\Org\Division;
use App\Models\Admin\ProcessApproval;
use App\Models\Merchandising\Costing\Costing;
use App\Models\Merchandising\PoOrderHeader;
use App\Models\Store\GrnHeader;
use DB;

class DashBoardController extends Controller
{
    public function index(Request $request)
    {
       if($request->type == 'customer-order-data'){
            //$this->loadCustomerOrderData();
           return response([
               'cus_data' => $this->loadCustomerOrderData()
           ]);
       }elseif($request->type == 'customer-order-detail'){

           return response([
               'cus_data' => $this->loadCustomerOrderDetails($request->customer)
           ]);
       }elseif($request->type == 'pending-costing-detail'){

           return response([
               'cus_data' => $this->loadPendingCostingDetails()
           ]);
       }elseif($request->type == 'edit-mode-data'){

           return response([
               'cus_data' => $this->loadEditModeDetails()
           ]);
       }elseif($request->type == 'load-po-approval'){

           return response([
               'cus_data' => $this->loadPoApprovalData()
           ]);
       }elseif($request->type == 'load-smv-update'){
           return response([
               'cus_data' => $this->loadPendingSmvData($request->customer)
           ]);
       }elseif($request->type == 'load-order-status'){

           return response([
               'cus_data' => $this->loadOrderStatus()
           ]);
       }elseif($request->type == 'load-pending-grn'){

           return response([
               'cus_data' => $this->loadPendingGrnData()
           ]);
       }


    }

    public function loadPendingGrnData(){
        $style = PoOrderHeader::select(DB::raw('COUNT(merc_po_order_header.po_id) as count'))
            ->join('store_grn_header', 'store_grn_header.po_number', '<>', 'merc_po_order_header.po_id')
            ->where('merc_po_order_header.status', '=', 1)
            //->groupBy('style_creation.customer_id')
            ->get()
            ->toArray();

        return $style;
    }

    public function loadOrderStatus(){
        //$customer['customers'] = Customer::pluck('customer_name')->toArray();
        $style = StyleCreation::select('style_creation.customer_id', 'cust_customer.customer_name',DB::raw('COUNT(style_id) as count'))
            ->join('cust_customer', 'cust_customer.customer_id', '=', 'style_creation.customer_id')
            ->where('style_creation.status', '=', 1)
            ->groupBy('style_creation.customer_id')
            ->get()
            ->toArray();

        return $style;
    }

    public function loadPendingSmvData($custId){
        $custDivisions = Customer::select('cust_customer.customer_code', 'cust_division.division_description')
            ->join('cust_division', 'cust_division.customer_code', '=', 'cust_customer.customer_code')
            ->where('cust_customer.customer_id', '=', 3)
            ->where('cust_division.status', '=', 1)
            ->groupBy('cust_customer.customer_id')
            ->get()
            ->toArray();


        /*$custDivisions = Division::select('cust_division.division_description', 'cust_division.division_id', 'cust_division.customer_code')
            ->join('cust_customer','cust_division.customer_code', '=', 'cust_division.customer_code' )
            ->where('cust_customer.customer_id', '=', $custId)
            ->where('cust_division.status', '=', 1)
            //->get()
            ->toSql();*/

        //dd($custDivisions);
        $output = array();

        foreach($custDivisions as $division ){

            $upCount = StyleCreation::select(DB::raw("count(`ie_component_smv_header`.style_id) as updated"), "cust_division.division_description")
                ->join('ie_component_smv_header','ie_component_smv_header.style_id', '=', 'style_creation.style_id' )
                ->join('cust_division','cust_division.division_id', '=', 'style_creation.division_id' )
                ->where('style_creation.status', '=', 1)
                ->where('style_creation.customer_id', '=', $custId)
                ->where('cust_division.customer_code', '=', $division['customer_code'])
                ->groupBy('style_creation.division_id')
                ->get();


            //$smvData[$division['customer_code']]['updated']
            $output['updated'][$division['division_description']]= $upCount[0]['updated'];


            $pendCount = StyleCreation::select(DB::raw("count(`ie_component_smv_header`.style_id) as pending"), "cust_division.division_description")
                ->join('ie_component_smv_header','ie_component_smv_header.style_id', '<>', 'style_creation.style_id' )
                ->join('cust_division','cust_division.division_id', '=', 'style_creation.division_id' )
                ->where('style_creation.status', '=', 1)
                ->where('style_creation.customer_id', '=', $custId)
                ->groupBy('style_creation.division_id')
                ->get()
                ->toArray();

            $output['pending'][$division['division_description']]= $upCount[0]['updated'];
            //$smvData[$division['customer_code']]['pending'] = $upCount[0]['pending'];
        }
        $output['divisions'] = $custDivisions;

        return $output;
    }

    public function loadPoApprovalData(){
        $approvalData['pending'] = PoOrderHeader::select(DB::raw("count(merc_po_order_header.po_id) as pending"))
            ->where('merc_po_order_header.created_by', '=', auth()->user()->user_id)
            ->where('merc_po_order_header.po_status', '=', 'PLANNED')
            ->get()
            ->toArray();



        $approvalData['approved'] = PoOrderHeader::select(DB::raw("count(merc_po_order_header.po_id) as approved"))
            ->where('merc_po_order_header.created_by', '=', auth()->user()->user_id)
            ->where('merc_po_order_header.po_status', '=', 'CONFIRMED')
            ->get()
            ->toArray();

        return $approvalData;
    }

    public function loadEditModeDetails(){
        $editabaledata['po'] = PoOrderHeader::select(DB::raw("count(merc_po_order_header.po_id) as poCount"))
            ->where('merc_po_order_header.created_by', '=', auth()->user()->user_id)
            ->get()
            ->toArray();

        $editabaledata['costing'] = Costing::select(DB::raw("count(id) as costingCount"))
            ->where('costing.created_by', '=', auth()->user()->user_id)
            ->get()
            ->toArray();

       return $editabaledata;
    }

    public function loadPendingCostingDetails(){
        //$customer['users'] = Customer::pluck('customer_name')->toArray();

        $pendCosting['costing'] = ProcessApproval::select('app_process_approval.status',DB::raw("count(app_process_approval.id) as count"))
            ->join('usr_profile', 'usr_profile.user_id', '=', 'app_process_approval.document_created_by')
            ->where('app_process_approval.document_created_by', '=', auth()->user()->user_id)
            ->groupBy('app_process_approval.status')
            ->get()
            ->toArray();

        return $pendCosting;
    }

    public function loadCustomerOrderData(){
        //$customer = Customer::select('customer_name')->get()->toArray();
        $customer['customers'] = Customer::pluck('customer_name')->toArray();
        $customer['customers'] = CustomerOrder::select('cust_customer.customer_name', 'cust_customer.customer_id',DB::raw("ROUND(merc_customer_order_details.fob*merc_customer_order_details.order_qty, 2) as total"))
            ->join('cust_customer', 'cust_customer.customer_id', '=', 'merc_customer_order_header.order_customer')
            ->join('merc_customer_order_details', 'merc_customer_order_details.order_id', '=', 'merc_customer_order_header.order_id')
            ->groupBy('cust_customer.customer_name')
            ->get()
            ->toArray();

        return $customer;
    }

    public function loadCustomerOrderDetails($customer){
        $so['divisions'] = Division::pluck('division_description')->toArray();

        $so['div_data'] = CustomerOrder::select('cust_division.division_description', 'cust_division.division_id',DB::raw("ROUND(merc_customer_order_details.fob*merc_customer_order_details.order_qty, 2) as total"))
            ->join('cust_customer', 'cust_customer.customer_id', '=', 'merc_customer_order_header.order_customer')
            ->join('cust_division', 'cust_customer.customer_code', '=', 'cust_division.customer_code')
            ->join('merc_customer_order_details', 'merc_customer_order_details.order_id', '=', 'merc_customer_order_header.order_id')
            ->where('cust_customer.customer_id', '=', $customer)
            ->groupBy('cust_customer.customer_name')
            ->get()
            ->toArray();

        return $so;
    }
}
