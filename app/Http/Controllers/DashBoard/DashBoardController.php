<?php

namespace App\Http\Controllers\DashBoard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Merchandising\CustomerOrder;
use App\Models\Org\Customer;
use App\Models\Org\Division;
use App\Models\Admin\ProcessApproval;
use App\Models\Merchandising\Costing\Costing;
use App\Models\Merchandising\PoOrderHeader;
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
       }


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
        //return response([ 'customers' => $customer ]);
        //return 'true';

       // dd($customer); ,
        $customer['customers'] = CustomerOrder::select('cust_customer.customer_name', 'cust_customer.customer_id',DB::raw("ROUND(merc_customer_order_details.fob*merc_customer_order_details.order_qty, 2) as total"))
            ->join('cust_customer', 'cust_customer.customer_id', '=', 'merc_customer_order_header.order_customer')
            ->join('merc_customer_order_details', 'merc_customer_order_details.order_id', '=', 'merc_customer_order_header.order_id')
            ->groupBy('cust_customer.customer_name')
            ->get()
            ->toArray();

       //dd($customer['customers']);
        return $customer;
    }

    public function loadCustomerOrderDetails($customer){
        $so['divisions'] = Division::pluck('division_description')->toArray();
        //return response([ 'customers' => $customer ]);
        //return 'true';
        //dd($customer);

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
