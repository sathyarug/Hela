<?php

namespace App\Http\Controllers\Merchandising;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use App\Models\Merchandising\CustomerOrderDetails;
use App\Models\Merchandising\CustomerOrderSize;
//use App\Libraries\UniqueIdGenerator;
use App\Models\Merchandising\StyleCreation;

class CustomerOrderDetailsController extends Controller
{
    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index','show']]);
    }

    //get customer list
    public function index(Request $request)
    {
      //$id_generator = new UniqueIdGenerator();
      //echo $id_generator->generateCustomerOrderDetailsId('CUSTOMER_ORDER' , 1);
      //echo UniqueIdGenerator::generateUniqueId('CUSTOMER_ORDER' , 2 , 'FDN');
      $type = $request->type;
      if($type == 'datatable') {
        $data = $request->all();
        return response($this->datatable_search($data));
      }
      else if($type == 'auto')  {
        $search = $request->search;
        return response($this->autocomplete_search($search));
      }
      else if($type == 'style_colors')  {
        $style = $request->style;
        return response(['data' => $this->style_colors($style)]);
      }else if($type == 'customer_po_for_so')  {
          $cusOrder = $request->order_id;
          $fields = $request->fields;
          return response([
              'data' => $this->getCustomerPoForSO($cusOrder, $fields)
          ]);
      }
      else{
        $order_id = $request->order_id;
        return response(['data' => $this->list($order_id)]);
      }
    }


    //create a customer
    public function store(Request $request)
    {
      $order_details = new CustomerOrderDetails();
      if($order_details->validate($request->all()))
      {
        $order_details->fill($request->all());
        $order_details->version_no = 0;
        $order_details->line_no = $this->get_next_line_no($order_details->order_id);
        $order_details->type_created = 'CREATE';
        $order_details->save();
        //$order_details = CustomerOrderDetails::with(['order_country','order_location'])->find($order_details->details_id);
        $order_details = $this->get_delivery_details($order_details->details_id);

        return response([ 'data' => [
          'message' => 'Customer order line was saved successfully',
          'customerOrderDetails' => $order_details
          ]
        ], Response::HTTP_CREATED );
      }
      else
      {
          $errors = $order_details->errors();// failure, get errors
          return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }


    //get a customer
    public function show($id)
    {
      $detail = CustomerOrderDetails::with(['order_country','order_location'])->find($id);
      if($detail == null)
        throw new ModelNotFoundException("Requested order details not found", 1);
      else
        return response([ 'data' => $detail ]);
    }


    //update a customer
    public function update(Request $request, $id)
    {
        $order_details = CustomerOrderDetails::find($id);
        $message = '';
      //    echo json_encode($order_details);die();
        if($order_details->validate($request->all()))
        {

          $order_details_new = new CustomerOrderDetails();
          $order_details_new->fill($request->all());
          $order_details_new->version_no = $order_details->version_no + 1;
          $order_details_new->line_no = $order_details->line_no;
          $order_details_new->type_created = $order_details->type_created;
          $order_details_new->type_modified = $order_details->type_modified;
          $order_details_new->parent_line_no = $order_details->parent_line_no;
          $order_details_new->parent_line_id = $order_details->parent_line_id;
          $order_details_new->split_lines = $order_details->split_lines;
          $order_details_new->merged_line_nos = $order_details->merged_line_nos;
          $order_details_new->merged_line_ids = $order_details->merged_line_ids;
          $order_details_new->merge_generated_line_id = $order_details->merge_generated_line_id;

          $order_details_new->save();

          $balance = $order_details_new->planned_qty - $order_details->planned_qty;
          if($order_details_new->order_qty == $order_details->order_qty){
              $sizes = CustomerOrderSize::where('details_id','=',$order_details->details_id)->get();
              foreach($sizes as $size){
                $new_size = new CustomerOrderSize();
                $new_size->details_id = $order_details_new->details_id;
                $new_size->size_id = $size->size_id;
                $new_size->order_qty = $size->order_qty;
                $new_size->excess_presentage = $size->excess_presentage;
                $new_size->planned_qty = $size->planned_qty;
                $new_size->version_no = $size->version_no;
                $new_size->line_no = $size->line_no;
                $new_size->save();
              }
              $message = 'Customer order line was saved successfully';
          }
          else{
              $message = 'Customer order line was saved successfully. But planned qty mismatch. Please enter size qty again.';
          }
          //$order_details_new = CustomerOrderDetails::with(['order_country','order_location'])->find($order_details_new->details_id);
          $order_details_new = $this->get_delivery_details($order_details_new->details_id);

          return response([ 'data' => [
            'message' => $message,
            'customerOrderDetails' => $order_details_new
            ]
          ], Response::HTTP_CREATED );
        }
        else
        {
            $errors = $order_details->errors();// failure, get errors
            return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }


    //deactivate a customer
    public function destroy($id)
    {
      /*$customer = Customer::where('customer_id', $id)->update(['status' => 0]);*/
      return response([
        'data' => [
          'message' => 'Customer was deactivated successfully.',
          'customer' => null
        ]
      ] , Response::HTTP_NO_CONTENT);
    }


    //validate anything based on requirements
    public function validate_data(Request $request){
      /*$for = $request->for;
      if($for == 'duplicate')
      {
        return response($this->validate_duplicate_code($request->customer_id , $request->customer_code));
      }*/
    }


    public function customer_divisions(Request $request) {
        /*$type = $request->type;
        $customer_id = $request->customer_id;

        if($type == 'selected')
        {
          $selected = Division::select('division_id','division_description')
          ->whereIn('division_id' , function($selected) use ($customer_id){
              $selected->select('division_id')
              ->from('org_customer_divisions')
              ->where('customer_id', $customer_id);
          })->get();
          return response([ 'data' => $selected]);
        }
        else
        {
          $notSelected = Division::select('division_id','division_description')
          ->whereNotIn('division_id' , function($notSelected) use ($customer_id){
              $notSelected->select('division_id')
              ->from('org_customer_divisions')
              ->where('customer_id', $customer_id);
          })->get();
          return response([ 'data' => $notSelected]);
        }*/

    }

    public function getCustomerPoForSO($cusOrder, $fields){
        $fields = explode(',', $fields);

        $customerOrder = CustomerOrderDetails::select($fields);
        $customerOrder->where('order_id','=',$cusOrder);
        return $customerOrder->get();
    }

    public function split_delivery(Request $request)
    {
      $split_count = $request->split_count;
      $delivery_id = $request->delivery_id;

      $delivery = CustomerOrderDetails::find($delivery_id);
      $excess_presentage = $delivery->excess_presentage;

      //$split_order_qty = ceil($delivery->order_qty / $split_count);
     // $split_plan_qty = ceil((($split_order_qty * $delivery->excess_presentage) / 100) + $split_order_qty);

      $sizes = CustomerOrderSize::where('details_id','=',$delivery->details_id)->get();//get all sizes belongs to current delivery
      $new_delivery_ids = [];

      for($x = 0 ; $x < $split_count ; $x++) //create new lines
      {
        $delivery_new = new CustomerOrderDetails();
        $delivery_new->order_id = $delivery['order_id'];
        $delivery_new->style_color = $delivery['style_color'];
        $delivery_new->pcd = $delivery['pcd'];
        $delivery_new->rm_in_date = $delivery['rm_in_date'];
        $delivery_new->po_no = $delivery['po_no'];
        $delivery_new->planned_delivery_date = $delivery['planned_delivery_date'];
        $delivery_new->projection_location = $delivery['projection_location'];
        $delivery_new->fob = $delivery['fob'];
        $delivery_new->country = $delivery['country'];
        $delivery_new->excess_presentage = $delivery['excess_presentage'];
        $delivery_new->ship_mode = $delivery['ship_mode'];
        $delivery_new->delivery_status = $delivery['delivery_status'];
        //$delivery_new->order_qty = $split_order_qty;
        //$delivery_new->planned_qty = $split_plan_qty;
        $delivery_new->line_no = $this->get_next_line_no($delivery->order_id);
        $delivery_new->version_no = 0;
        $delivery_new->parent_line_id = $delivery->details_id;
        $delivery_new->parent_line_no = $delivery->line_no;
        $delivery_new->type_created = 'GFS';
        $delivery_new->save();

        array_push($new_delivery_ids , $delivery_new->details_id);
        $split_order_qty = 0;
        $split_plan_qty = 0;

        foreach($sizes as $size){ //create new sizes with new order qty
          $order_qty2 = $size->order_qty;
          //$excess_presentage = $size['excess_presentage'];
          $split_order_qty2 = ceil($order_qty2 / $split_count);
          $split_planned_qty2 = ceil((($split_order_qty2 * $excess_presentage) / 100) + $split_order_qty2);

          $split_order_qty += $split_order_qty2;
          $split_plan_qty += $split_planned_qty2;

          $new_size = new CustomerOrderSize();
          $new_size->details_id = $delivery_new->details_id;
          $new_size->size_id = $size->size_id;
          $new_size->order_qty = $split_order_qty2;
          $new_size->excess_presentage = $excess_presentage;
          $new_size->planned_qty = $split_planned_qty2;
          $new_size->version_no = 0;
          $new_size->line_no = 1;
          $new_size->save();
        }

        $delivery_new->order_qty = $split_order_qty;//update order qty and planned qty
        $delivery_new->planned_qty = $split_plan_qty;
        $delivery_new->save();
      }

      $new_delivery_ids_str = json_encode($new_delivery_ids);
      $delivery->split_lines = $new_delivery_ids_str;
      $delivery->delivery_status = 'CANCEL';
      $delivery->type_modified = 'SPLIT';
      $delivery->save();

      return response([ 'data' => [
        'message' => 'Delivery was splited successfully'/*,
        'customerOrderDetails' => $order_details*/
        ]
      ], Response::HTTP_CREATED );

    }


    public function merge(Request $request){
      $lines = $request->lines;
      if($lines != null && sizeof($lines) > 1){
        $merge_order_qty = 0;
        $merge_planned_qty = 0;
        $merged_lines = [];
        $merged_ids = [];

        for($x = 0 ; $x < sizeof($lines) ; $x++){
          $delivery = CustomerOrderDetails::find($lines[$x]);
          $merge_order_qty += $delivery['order_qty'];
          $merge_planned_qty += $delivery['planned_qty'];
          array_push($merged_lines , $delivery->line_no);
          array_push($merged_ids , $delivery->details_id);
        }

        $first = CustomerOrderDetails::find($lines[0]);
        $delivery_new = new CustomerOrderDetails();

        $delivery_new->order_id = $first['order_id'];
        $delivery_new->style_color = $first['style_color'];
        $delivery_new->pcd = $first['pcd'];
        $delivery_new->rm_in_date = $first['rm_in_date'];
        $delivery_new->po_no = $first['po_no'];
        $delivery_new->planned_delivery_date = $first['planned_delivery_date'];
        $delivery_new->projection_location = $first['projection_location'];
        $delivery_new->fob = $first['fob'];
        $delivery_new->country = $first['country'];
        $delivery_new->excess_presentage = $first['excess_presentage'];
        $delivery_new->ship_mode = $first['ship_mode'];
        $delivery_new->delivery_status = $first['delivery_status'];
        $delivery_new->order_qty = $merge_order_qty;
        $delivery_new->planned_qty = $merge_planned_qty;
        $delivery_new->line_no = $this->get_next_line_no($first->order_id);
        $delivery_new->version_no = 0;
        $delivery_new->merged_line_nos = json_encode($merged_lines);
        $delivery_new->merged_line_ids = json_encode($merged_ids);
        $delivery_new->type_created = 'GFM';
        $delivery_new->save();

        //$new_sizes = [];
        for($x = 0 ; $x < sizeof($lines) ; $x++){
          $delivery = CustomerOrderDetails::find($lines[$x]);
          $delivery->delivery_status = 'CANCEL';
          $delivery->type_modified = 'MERGE';
          $delivery->merge_generated_line_id = $delivery_new->details_id;
          $delivery->save();
        }

        //$ids_str = implode(',',$merged_ids);
        /*$sizes = DB::select("SELECT size_id,SUM(order_qty) AS total_order_qty,SUM(planned_qty) AS total_planned_qty FROM merc_customer_order_size
        WHERE details_id IN (".$ids_str.") GROUP BY size_id" , [$ids_str]);*/
        $sizes = DB::table('merc_customer_order_size')
                 ->select(DB::raw('size_id,SUM(order_qty) AS total_order_qty,SUM(planned_qty) AS total_planned_qty'))
                 ->whereIn('details_id', $merged_ids)
                 ->groupBy('size_id')
                 ->get();

        for($y = 0 ; $y < sizeof($sizes) ; $y++){
          $size_new = new CustomerOrderSize();

          $size_new->details_id = $delivery_new->details_id;
          $size_new->size_id = $sizes[$y]->size_id;
          $size_new->order_qty = $sizes[$y]->total_order_qty;
          $size_new->planned_qty = $sizes[$y]->total_planned_qty;
          $size_new->excess_presentage = 0;
          $size_new->line_no = $this->get_next_size_line_no($delivery_new->details_id);
          $size_new->version_no = 0;
          $size_new->save();
        }

        return response([
          'data' => [
            'status' => 'success',
            'message' => 'Lines were merged successfully.'
          ]
        ] , 200);
      }
      else{
        return response([
          'data' => [
            'status' => 'error',
            'message' => 'Incorrect details'
          ]
        ] , Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }


    public function revisions(Request $request){
        $delivery = CustomerOrderDetails::find($request->details_id);
        $deliveries = [];
        if($delivery != null){
          $deliveries = CustomerOrderDetails::where('order_id', '=', $delivery->order_id)
          ->where('line_no', '=', $delivery->line_no)
          ->get();
        }
        return response([
          'data' => $deliveries
        ]);
    }


    public function origins(Request $request){
        $delivery = CustomerOrderDetails::find($request->details_id);
        $deliveries = [];
        if($delivery != null){

          if($delivery->type_created == 'GFS'){
            $deliveries = CustomerOrderDetails::where('details_id', '=', $delivery->parent_line_id)
            ->get();
          }
          else if($delivery->type_created == 'GFM'){
            $merged_lines = json_decode($delivery->merged_line_ids);
            //print_r($delivery->details_id);die();
            $deliveries = CustomerOrderDetails::whereIn('details_id', $merged_lines)
            ->get();
          }

       }

        return response([
          'data' => $deliveries
        ]);
    }


    //check customer code already exists
    private function validate_duplicate_code($id , $code)
    {
      /*$customer = Customer::where('customer_code','=',$code)->first();
      if($customer == null){
        return ['status' => 'success'];
      }
      else if($customer->customer_id == $id){
        return ['status' => 'success'];
      }
      else {
        return ['status' => 'error','message' => 'Customer code already exists'];
      }*/
    }


    //search customer for autocomplete
    private function autocomplete_search($search)
  	{
  		$co_lists = CustomerOrderDetails::select('order_id','po_no')
  		->where([['po_no', 'like', '%'.$search.'%'],])->distinct()->get();
  		return $co_lists;
  	}


    //search customer for autocomplete
    private function style_search($search)
  	{
  		$style_lists = StyleCreation::select('style_id','style_no','customer_id')
  		->where([['style_no', 'like', '%' . $search . '%'],]) ->get();
  		return $style_lists;
  	}


    private function list($order_id){
      /*$order_details = CustomerOrderDetails::join('org_color','merc_customer_order_details.style_color','=','org_color.color_id')
      ->join('org_country','merc_customer_order_details.country','=','org_country.country_id')
      ->join('org_location','merc_customer_order_details.projection_location','=','org_location.loc_id')
      ->select('merc_customer_order_details.*','org_color.color_code','org_color.color_name','org_country.country_description','org_location.loc_name')
      ->where('merc_customer_order_details.order_id','=',$order_id)
      ->get();*/
      /*$order_details = CustomerOrderDetails::with(['order_country','order_location'])
      ->where('order_id','=',$order_id)
      ->where('delivery_status' , '!=' , 'CANCEL')
      ->where(function($q) use ($order_id) {
        $q->where('version_no', function($q) use ($order_id)
          {
             $q->from('merc_customer_order_details')
              ->selectRaw('MAX(version_no)')
              ->where('order_id', '=', $order_id)
          });
      })
      ->get();*/
      $order_details = DB::select('select a.*,org_country.country_description,org_location.loc_name
       from merc_customer_order_details a
      inner join org_country on a.country = org_country.country_id
      inner join org_location on a.projection_location = org_location.loc_id
      where
      a.order_id = ? and
      a.delivery_status != ? and
      a.version_no = (select MAX(b.version_no) from merc_customer_order_details b where b.order_id = a.order_id and a.line_no=b.line_no)
      order by a.line_no',
      [$order_id , 'CANCEL']);
      return $order_details;
    }


    private function style_colors($style){
    /*  $colors = DB::select("SELECT costing_bulk_feature_details.combo_color, org_color.color_code,org_color.color_name FROM costing_bulk_feature_details
          INNER JOIN costing_bulk ON costing_bulk.bulk_costing_id = costing_bulk_feature_details.bulkheader_id
          INNER JOIN org_color ON costing_bulk_feature_details.combo_color = org_color.color_id
          WHERE costing_bulk.style_id = ?",[$style]);*/
      $colors = DB::select("SELECT color_id,color_code,color_name from org_color");
      return $colors;
    }


    //get searched customers for datatable plugin format
    private function datatable_search($data)
    {
      /*$start = $data['start'];
      $length = $data['length'];
      $draw = $data['draw'];
      $search = $data['search']['value'];
      $order_details = $data['order'][0];
      $order_details_column = $data['columns'][$order_details['column']]['data'];
      $order_details_type = $order_details['dir'];

      $customer_list = Customer::select('*')
      ->where('customer_code'  , 'like', $search.'%' )
      ->orWhere('customer_name'  , 'like', $search.'%' )
      ->orWhere('customer_short_name'  , 'like', $search.'%' )
      ->orderBy($order_details_column, $order_details_type)
      ->offset($start)->limit($length)->get();

      $customer_count = Customer::where('customer_code'  , 'like', $search.'%' )
      ->orWhere('customer_name'  , 'like', $search.'%' )
      ->orWhere('customer_short_name'  , 'like', $search.'%' )
      ->count();

      return [
          "draw" => $draw,
          "recordsTotal" => $customer_count,
          "recordsFiltered" => $customer_count,
          "data" => $customer_list
      ];*/
    }


    //get searched customers for datatable plugin format
    /*private function get_next_version_no($details_id)
    {
      $max_id = CustomerOrderSize::where('details_id','=',$details_id)->max('version_no');
      return ($max_id + 1);
    }*/

    private function get_delivery_details($details_id){
      $deliveries = CustomerOrderDetails::join('org_country', 'org_country.country_id', '=', 'merc_customer_order_details.country')
      ->join('org_location', 'org_location.loc_id', '=', 'merc_customer_order_details.projection_location')
      ->select('merc_customer_order_details.*','org_country.country_description','org_location.loc_name')
      ->where('merc_customer_order_details.details_id', '=', $details_id)
      ->first();
      return $deliveries;
    }


    private function get_next_line_no($order_id)
    {
      $max_no = CustomerOrderDetails::where('order_id','=',$order_id)->max('line_no');
      return ($max_no + 1);
    }


    //get searched customers for datatable plugin format
    private function get_next_size_line_no($details_id)
    {
      $max_no = CustomerOrderSize::where('details_id','=',$details_id)->max('line_no');
      return ($max_no + 1);
    }


}
