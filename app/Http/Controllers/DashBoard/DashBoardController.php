<?php

namespace App\Http\Controllers\DashBoard;

use App\Models\Merchandising\StyleCreation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Merchandising\CustomerOrder;
use App\Models\Merchandising\CustomerOrderDetails;
use App\Models\Org\Customer;
use App\Models\Org\Division;
use App\Models\Admin\ProcessApproval;
use App\Models\Merchandising\Costing\Costing;
use App\Models\Merchandising\PoOrderHeader;
use App\Models\Merchandising\Item\Item;
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
       }elseif($request->type == 'load-item-creation'){

           return response([
               'cus_data' => $this->loadItemApprovalData()
           ]);
       }


    }

    public function loadItemApprovalData(){
        $itemApp = Item::select('master_id')
            ->where('status', '=', 1)
            ->whereNull('approval_status')
            ->where('created_by', '=', auth()->user()->user_id)
            ->get()
            ->toArray();

        return count($itemApp);

    }

    public function loadPendingGrnData(){
        $grn = DB::select('SELECT
                    `merc_customer_order_details`.`order_id`,
                    `store_grn_detail`.`grn_id`
                FROM
                    `merc_customer_order_details`
                INNER JOIN `store_grn_detail` ON `store_grn_detail`.`shop_order_id` = `merc_customer_order_details`.`shop_order_id`
                INNER JOIN merc_customer_order_header ON `merc_customer_order_header`.`order_id` = `merc_customer_order_details`.`order_id`
                WHERE
                    `merc_customer_order_details`.`created_by` = '.auth()->user()->user_id.'
                    AND merc_customer_order_details.rm_in_date < NOW()
                    AND merc_customer_order_header.order_status = "PLANNED"
                GROUP BY
                    `merc_customer_order_details`.`order_id`
                HAVING
                    `store_grn_detail`.`grn_id` IS NOT NULL');

        $response['grn'] = count($grn);

        $pendGrn = DB::select('SELECT
                    `merc_customer_order_details`.`order_id`
                FROM
                    `merc_customer_order_details`
                WHERE
                    `merc_customer_order_details`.`created_by` = '.auth()->user()->user_id.'
                    AND merc_customer_order_details.rm_in_date < NOW()
                GROUP BY
	                `merc_customer_order_details`.`order_id`');

        $response['non_grn'] = count($pendGrn);

        return $response;
    }

    public function loadOrderStatus(){
        //$customer['customers'] = Customer::pluck('customer_name')->toArray();
        $style = StyleCreation::select('style_creation.customer_id', 'cust_customer.customer_name',DB::raw('COUNT(style_creation.style_id) as count'))
            ->join('cust_customer', 'cust_customer.customer_id', '=', 'style_creation.customer_id')
            ->join('ie_component_smv_header', 'style_creation.style_id', '=', 'ie_component_smv_header.style_id')
            ->where('style_creation.status', '=', 1)
            ->whereNotNull('ie_component_smv_header.total_smv')
            ->groupBy('style_creation.customer_id')
            ->get()
            ->toArray();

        $pending = StyleCreation::select('style_creation.customer_id', 'cust_customer.customer_name',DB::raw('COUNT(*) as count'))
            ->join('cust_customer', 'cust_customer.customer_id', '=', 'style_creation.customer_id')
            ->leftjoin('ie_component_smv_header', 'style_creation.style_id', '=', 'ie_component_smv_header.style_id')
            ->where('style_creation.status', '=', 1)
            ->whereNull('ie_component_smv_header.total_smv')
            ->groupBy('style_creation.customer_id')
            ->groupBy('ie_component_smv_header.style_id')
            ->get()
            ->toArray();

        return $pending;
    }

    public function loadPendingSmvData($custId){
        $custDivisions = Customer::select('cust_customer.customer_code', 'cust_division.division_description')
            ->join('cust_division', 'cust_division.customer_code', '=', 'cust_customer.customer_code')
            ->where('cust_customer.customer_id', '=', $custId)
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

            $upCount = StyleCreation::select('ie_component_smv_header.style_id', "cust_division.division_description")
                ->join('ie_component_smv_header','ie_component_smv_header.style_id', '=', 'style_creation.style_id' )
                ->join('cust_division','cust_division.division_id', '=', 'style_creation.division_id' )
                ->where('style_creation.status', '=', 1)
                ->where('style_creation.customer_id', '=', $custId)
                ->where('cust_division.customer_code', '=', $division['customer_code'])
                ->groupBy('style_creation.division_id')
                ->groupBy('ie_component_smv_header.style_id')
                ->get();

            $output['updated'][$division['division_description']]= count($upCount);


            $pendCount = DB::select('SELECT
                                    `style_creation`.style_id,	
                                    `cust_division`.`division_description`,
                                    ie_component_smv_header.total_smv
                                FROM
                                    `style_creation`
                                LEFT JOIN `ie_component_smv_header` ON `ie_component_smv_header`.`style_id` = `style_creation`.`style_id`
                                INNER JOIN `cust_division` ON `cust_division`.`division_id` = `style_creation`.`division_id`
                                WHERE
                                    `style_creation`.`status` = 1
                                AND `style_creation`.`customer_id` = '.$custId.'
                                AND  `cust_division`.`customer_code` = "'.$division['customer_code'].'"
                                GROUP BY
                                    `style_creation`.`division_id`
                                HAVING total_smv IS NULL');

            $output['pending'][$division['division_description']]= count($pendCount);;

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
