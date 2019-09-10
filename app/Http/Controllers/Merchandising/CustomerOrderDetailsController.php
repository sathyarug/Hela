<?php

namespace App\Http\Controllers\Merchandising;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use App\Models\Merchandising\CustomerOrderDetails;
use App\Models\Merchandising\CustomerOrder;
use App\Models\Merchandising\CustomerOrderSize;
//use App\Libraries\UniqueIdGenerator;
use App\Models\Merchandising\StyleCreation;
use App\Models\Merchandising\Costing\Costing;
use App\Libraries\CapitalizeAllFields;

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

        $check_duplicate = CustomerOrderDetails::select('*')
           ->where('style_color' , '=', $request->style_color )
           ->where('pcd' , '=', $request->pcd )
           ->where('rm_in_date' , '=', $request->rm_in_date )
           ->where('po_no' , '=', $request->po_no )
           ->where('planned_delivery_date' , '=', $request->planned_delivery_date )
           ->where('fob' , '=', $request->fob )
           ->where('country' , '=', $request->country )
           ->where('projection_location' , '=', $request->projection_location )
           ->where('order_qty' , '=', $request->order_qty )
           ->where('excess_presentage' , '=', $request->excess_presentage )
           ->where('ship_mode' , '=', $request->ship_mode )
           ->where('ex_factory_date' , '=', $request->ex_factory_date )
           ->where('colour_type' , '=', $request->colour_type )
           ->where('ac_date' , '=', $request->ac_date )
           ->where('cus_style_manual' , '=', $request->cus_style_manual )
           ->where('order_id' , '=', $request->order_id )
           ->where('delivery_status' , '<>', 'CANCELLED' )
           ->get();

           //dd(sizeof($check_duplicate));
           //dd($request);

           if(sizeof($check_duplicate) != 0){
             return response([ 'data' => ['status' => 'error','message' => 'Order Line Details already exist ..!']]);
           }

        $order_details->fill($request->all());
        $capitalizeAllFields=CapitalizeAllFields::setCapitalAll($order_details);
        $order_details->style_description = '';
        $order_details->delivery_status = 'PLANNED';
        $order_details->version_no = 0;
        $order_details->line_no = $this->get_next_line_no($order_details->order_id);
        $order_details->type_created = 'CREATE';
        $order_details->active_status = 'ACTIVE';
        $order_details->save();
        //$order_details = CustomerOrderDetails::with(['order_country','order_location'])->find($order_details->details_id);
        $order_details = $this->get_delivery_details($order_details->details_id);

        return response([ 'data' => [
          'message' => 'Sales order line saved successfully',
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
      $header = CustomerOrder::find($detail['order_id']);

      $colour_type = CustomerOrderDetails::select('merc_color_options.col_opt_id', 'merc_color_options.color_option')
                   ->join('merc_color_options', 'merc_customer_order_details.colour_type', '=', 'merc_color_options.col_opt_id')
                   ->where('merc_customer_order_details.details_id', '=', $id)
                   ->get();

      $detail['col_type'] = $colour_type;

      $st_colour = Costing::select('org_color.color_id', 'org_color.color_code')
                   ->join('costing_finish_goods', 'costing.id', '=', 'costing_finish_goods.costing_id')
                   ->join('org_color', 'costing_finish_goods.combo_color_id', '=', 'org_color.color_id')
                   ->where('style_id', '=', $header['order_style'])
                   ->where('bom_stage_id', '=', $header['order_stage'])
                   ->where('season_id', '=', $header['order_season'])
                   ->where('color_type_id', '=', $detail['colour_type'])
                   ->get();

      $detail['style_colour']  = $st_colour;

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
          $check_duplicate = CustomerOrderDetails::select('*')
             ->where('style_color' , '=', $request->style_color )
             ->where('pcd' , '=', $request->pcd )
             ->where('rm_in_date' , '=', $request->rm_in_date )
             ->where('po_no' , '=', $request->po_no )
             ->where('planned_delivery_date' , '=', $request->planned_delivery_date )
             ->where('fob' , '=', $request->fob )
             ->where('country' , '=', $request->country )
             ->where('projection_location' , '=', $request->projection_location )
             ->where('order_qty' , '=', $request->order_qty )
             ->where('excess_presentage' , '=', $request->excess_presentage )
             ->where('ship_mode' , '=', $request->ship_mode )
             ->where('ex_factory_date' , '=', $request->ex_factory_date )
             ->where('colour_type' , '=', $request->colour_type )
             ->where('ac_date' , '=', $request->ac_date )
             ->where('cus_style_manual' , '=', $request->cus_style_manual )
             ->where('order_id' , '=', $request->order_id )
             ->where('delivery_status' , '<>', 'CANCELLED' )
             ->get();

             //dd(sizeof($check_duplicate));
             //dd($request);

             if(sizeof($check_duplicate) != 0){
               return response([ 'data' => ['status' => 'error','message' => 'Order Line Details already exist ..!']]);
             }

          $order_details_new = new CustomerOrderDetails();
          $order_details_new->fill($request->all());
          $order_details_new->delivery_status = $order_details->delivery_status;
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
          $order_details_new->active_status = 'ACTIVE';
          $order_details_new->save();

          DB::table('merc_customer_order_details')
              ->where('details_id', $id)
              ->update(['active_status' => 'INACTIVE']);



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
              $message = 'Sales order line updated successfully';
          }
          else{
              $message = 'Sales order line updated successfully. But planned qty mismatch. Please enter size qty again.';
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
          'message' => 'Sales deactivated successfully.',
          'customer' => null
        ]
      ] , Response::HTTP_NO_CONTENT);
    }

    public function copy_line(Request $request){

      $check = $request->check;
      $line_id = $request->line_id;

      $order_details = CustomerOrderDetails::find($line_id);

        $order_details_new = new CustomerOrderDetails();
        $order_details_new->order_id = $order_details->order_id;
        $order_details_new->style_color = $order_details->style_color;
        $order_details_new->style_description = $order_details->style_description;
        $order_details_new->pcd = $order_details->pcd;
        $order_details_new->rm_in_date = $order_details->rm_in_date;
        $order_details_new->po_no = $order_details->po_no;
        $order_details_new->planned_delivery_date = $order_details->planned_delivery_date;
        $order_details_new->fob = $order_details->fob;
        $order_details_new->country = $order_details->country;
        $order_details_new->projection_location = $order_details->projection_location;
        $order_details_new->order_qty = $order_details->order_qty;
        $order_details_new->excess_presentage = $order_details->excess_presentage;
        $order_details_new->planned_qty = $order_details->planned_qty;
        $order_details_new->ship_mode = $order_details->ship_mode;
        $order_details_new->delivery_status = 'PLANNED';
        $order_details_new->type_created = 'CREATE';
        $order_details_new->ex_factory_date = $order_details->ex_factory_date;
        $order_details_new->ac_date = $order_details->ac_date;
        $order_details_new->version_no = 0;
        $order_details_new->line_no = $this->get_next_line_no($order_details->order_id);

        $order_details_new->active_status = 'ACTIVE';
        $order_details_new->save();

        if($check == 1){

          $sizes = CustomerOrderSize::where('details_id','=',$line_id)->get();
          //echo $sizes ;
          for($y = 0 ; $y < sizeof($sizes) ; $y++){

            $new_size = new CustomerOrderSize();
            $new_size->details_id = $order_details_new->details_id;
            $new_size->size_id = $sizes[$y]['size_id'];
            $new_size->order_qty = $sizes[$y]['order_qty'];
            $new_size->excess_presentage = $sizes[$y]['excess_presentage'];
            $new_size->planned_qty = $sizes[$y]['planned_qty'];
            $new_size->version_no = $sizes[$y]['version_no'];
            $new_size->line_no = $sizes[$y]['line_no'];
            $new_size->status = $sizes[$y]['status'];
            $new_size->save();


          }




        }

        return response([
          'data' => [
            'status' => 'success',
            'message' => 'Successfully Copied.'
          ]
        ] , 200);


    }


    public function delete_line(Request $request){

      $details_id   = $request->details_id;
      $status       = $request->status;
      $order_id     = $request->order_id;

      if($status == 'PLANNED'){
        $line = CustomerOrderDetails::find($details_id);
        if($line['delivery_status'] == 'PLANNED'){
          $updateDetails = ['active_status' => 'INACTIVE','delivery_status' => 'CANCELLED'];
          CustomerOrderDetails::where('details_id', $details_id)->update($updateDetails);
        }else{
          return response([
            'data' => [
              'status' => 'error',
              'message' => "This line alredy connected with costing."
            ]
          ] , 200);
        }


      }
      else if ($status == 'CANCELLED'){

        CustomerOrderDetails::where('details_id', $details_id)
            ->update(['active_status' => 'INACTIVE']);

      }

      return response([
        'data' => [
          'status' => 'success',
          'message' => 'Successfully Deleted.'
        ]
      ] , 200);


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
      $lines       = $request->lines;

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
        $delivery_new->order_qty = $lines[$x]['order_qty'];
        $delivery_new->planned_qty = $lines[$x]['planned_qty'];
        $delivery_new->line_no = $this->get_next_line_no($delivery->order_id);
        $delivery_new->version_no = 0;
        $delivery_new->parent_line_id = $delivery->details_id;
        $delivery_new->parent_line_no = $delivery->line_no;
        $delivery_new->type_created = 'GFS';
        $delivery_new->ex_factory_date = $delivery['ex_factory_date'];
        $delivery_new->ac_date = $delivery['ac_date'];
        $delivery_new->active_status = 'ACTIVE';
        $delivery_new->costing_id = $delivery['costing_id'];
        $delivery_new->fg_id = $delivery['fg_id'];
        $delivery_new->colour_type = $delivery['colour_type'];
        $delivery_new->save();

          array_push($new_delivery_ids , $delivery_new->details_id);
        /*  $split_order_qty = 0;
          $split_plan_qty = 0;

        foreach($sizes as $size){ //create new sizes with new order qty
          $order_qty2 = $size->order_qty;
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

        if(sizeof($sizes) > 0){
          $delivery_new->order_qty = $split_order_qty;//update order qty and planned qty
          $delivery_new->planned_qty = $split_plan_qty;
        }
        else{ //no sizes avalibale and split main order qty
          $s_qty = ceil($delivery['order_qty'] / $split_count);
          $delivery_new->order_qty = $s_qty;
          $delivery_new->planned_qty = ceil((($s_qty * $excess_presentage) / 100) + $s_qty);
        } */

        $delivery_new->save();
      }

      $new_delivery_ids_str = json_encode($new_delivery_ids);
      $delivery->split_lines = $new_delivery_ids_str;
      $delivery->delivery_status = 'CANCELLED';
      $delivery->type_modified = 'SPLIT';
      $delivery->active_status = 'INACTIVE';
      $delivery->save();

      return response([ 'data' => [
        'message' => 'Delivery splited successfully'/*,
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
        $deli_st = [];
        $deli_check = null;

        for($x = 0 ; $x < sizeof($lines) ; $x++){
          $delivery = CustomerOrderDetails::find($lines[$x]);
          $merge_order_qty += $delivery['order_qty'];
          $merge_planned_qty += $delivery['planned_qty'];
          array_push($merged_lines , $delivery->line_no);
          array_push($merged_ids , $delivery->details_id);
          array_push($deli_st , $delivery->delivery_status);
        }

        $first = CustomerOrderDetails::find($lines[0]);
        $delivery_new = new CustomerOrderDetails();

        for($x = 0 ; $x < sizeof($deli_st) ; $x++)
        {
          if($deli_check != null  &&  $deli_check != $deli_st[$x])
            { $new_deli_status = 'CONNECTED'; }else{$new_deli_status=$first['delivery_status'];}
              $deli_check = $deli_st[$x];
        }

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
        $delivery_new->delivery_status = $new_deli_status;
        $delivery_new->order_qty = $merge_order_qty;
        $delivery_new->planned_qty = $merge_planned_qty;
        $delivery_new->line_no = $this->get_next_line_no($first->order_id);
        $delivery_new->version_no = 0;
        $delivery_new->merged_line_nos = json_encode($merged_lines);
        $delivery_new->merged_line_ids = json_encode($merged_ids);
        $delivery_new->type_created = 'GFM';
        $delivery_new->ex_factory_date = $delivery['ex_factory_date'];
        $delivery_new->ac_date = $delivery['ac_date'];
        $delivery_new->active_status = 'ACTIVE';
        $delivery_new->costing_id = $delivery['costing_id'];
        $delivery_new->fg_id = $delivery['fg_id'];
        $delivery_new->colour_type = $delivery['colour_type'];
        $delivery_new->save();

        //$new_sizes = [];
        for($x = 0 ; $x < sizeof($lines) ; $x++){
          $delivery = CustomerOrderDetails::find($lines[$x]);
          $delivery->delivery_status = 'CANCELLED';
          $delivery->type_modified = 'MERGE';
          //$delivery->active_status = 'INACTIVE';
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
            'message' => 'Lines merged successfully.'
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
          ->join('org_color', 'org_color.color_id', '=', 'merc_customer_order_details.style_color')
          ->join('org_country', 'org_country.country_id', '=', 'merc_customer_order_details.country')
          ->join('org_location','org_location.loc_id', '=', 'merc_customer_order_details.projection_location')
          ->select('org_location.loc_name','merc_customer_order_details.*', 'org_color.color_code', 'org_color.color_name','org_country.country_description',
          DB::raw("DATE_FORMAT(merc_customer_order_details.pcd, '%d-%b-%Y') 'pcd'"),
          DB::raw("DATE_FORMAT(merc_customer_order_details.ex_factory_date, '%d-%b-%Y') 'ex_factory_date'"),
          DB::raw("DATE_FORMAT(merc_customer_order_details.planned_delivery_date, '%d-%b-%Y') 'planned_delivery_date'"),
          DB::raw("DATE_FORMAT(merc_customer_order_details.rm_in_date, '%d-%b-%Y') 'rm_in_date'"),
          DB::raw("DATE_FORMAT(merc_customer_order_details.ac_date, '%d-%b-%Y') 'ac_date'")

          )
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
            ->join('org_color', 'org_color.color_id', '=', 'merc_customer_order_details.style_color')
            ->join('org_country', 'org_country.country_id', '=', 'merc_customer_order_details.country')
            ->join('org_location','org_location.loc_id', '=', 'merc_customer_order_details.projection_location')
            ->select('org_location.loc_name','merc_customer_order_details.*', 'org_color.color_code', 'org_color.color_name', 'org_country.country_description',
            DB::raw("DATE_FORMAT(merc_customer_order_details.pcd, '%d-%b-%Y') 'pcd'"),
            DB::raw("DATE_FORMAT(merc_customer_order_details.ex_factory_date, '%d-%b-%Y') 'ex_factory_date'"),
            DB::raw("DATE_FORMAT(merc_customer_order_details.planned_delivery_date, '%d-%b-%Y') 'planned_delivery_date'"),
            DB::raw("DATE_FORMAT(merc_customer_order_details.rm_in_date, '%d-%b-%Y') 'rm_in_date'"),
            DB::raw("DATE_FORMAT(merc_customer_order_details.ac_date, '%d-%b-%Y') 'ac_date'")
            )
            ->get();
          }
          else if($delivery->type_created == 'GFM'){
            $merged_lines = json_decode($delivery->merged_line_ids);
            //print_r($delivery->details_id);die();
            $deliveries = CustomerOrderDetails::whereIn('details_id', $merged_lines)
            ->join('org_color', 'org_color.color_id', '=', 'merc_customer_order_details.style_color')
            ->join('org_country', 'org_country.country_id', '=', 'merc_customer_order_details.country')
            ->join('org_location','org_location.loc_id', '=', 'merc_customer_order_details.projection_location')
            ->select('org_location.loc_name','merc_customer_order_details.*', 'org_color.color_code', 'org_color.color_name', 'org_country.country_description',
            DB::raw("DATE_FORMAT(merc_customer_order_details.pcd, '%d-%b-%Y') 'pcd'"),
            DB::raw("DATE_FORMAT(merc_customer_order_details.ex_factory_date, '%d-%b-%Y') 'ex_factory_date'"),
            DB::raw("DATE_FORMAT(merc_customer_order_details.planned_delivery_date, '%d-%b-%Y') 'planned_delivery_date'"),
            DB::raw("DATE_FORMAT(merc_customer_order_details.rm_in_date, '%d-%b-%Y') 'rm_in_date'"),
            DB::raw("DATE_FORMAT(merc_customer_order_details.ac_date, '%d-%b-%Y') 'ac_date'"))
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
      $order_details = DB::select("select DATE_FORMAT(a.ac_date, '%d-%b-%Y') as ac_date_01,
      DATE_FORMAT(a.rm_in_date, '%d-%b-%Y') as rm_in_date_01,
      DATE_FORMAT(a.planned_delivery_date, '%d-%b-%Y') as planned_delivery_date_01,
      DATE_FORMAT(a.ex_factory_date, '%d-%b-%Y') as ex_factory_date_01,
      DATE_FORMAT(a.pcd, '%d-%b-%Y') as pcd_01,
       a.*,(a.order_qty * a.fob) as total_value,
      org_country.country_description,org_location.loc_name,org_color.color_code,org_color.color_name
      from merc_customer_order_details a
      inner join org_country on a.country = org_country.country_id
      inner join org_location on a.projection_location = org_location.loc_id
      inner join org_color on a.style_color = org_color.color_id
      where
      a.order_id = ? and
      #a.delivery_status != ? and
      a.type_modified is null and
      a.version_no = (select MAX(b.version_no)
      from merc_customer_order_details b where b.order_id = a.order_id and a.line_no=b.line_no)

      order by a.active_status ASC,a.line_no ASC",
      [$order_id , 'CONNECTED']);
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
      ->join('org_color', 'org_color.color_id', '=', 'merc_customer_order_details.style_color')
      ->select(DB::raw("DATE_FORMAT(merc_customer_order_details.pcd, '%d-%b-%Y') 'pcd_01'"),DB::raw("DATE_FORMAT(merc_customer_order_details.ex_factory_date, '%d-%b-%Y') 'ex_factory_date_01'"),DB::raw("DATE_FORMAT(merc_customer_order_details.planned_delivery_date, '%d-%b-%Y') 'planned_delivery_date_01'"),DB::raw("DATE_FORMAT(merc_customer_order_details.rm_in_date, '%d-%b-%Y') 'rm_in_date_01'"),DB::raw("DATE_FORMAT(merc_customer_order_details.ac_date, '%d-%b-%Y') 'ac_date_01'"),'merc_customer_order_details.*','org_country.country_description','org_location.loc_name','org_color.color_code','org_color.color_name')
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

    public function load_colour_type(Request $request){

      $style_id = $request->style_id;
      $colour_type = Costing::select('merc_color_options.col_opt_id', 'merc_color_options.color_option')
                   ->join('merc_color_options', 'costing.color_type_id', '=', 'merc_color_options.col_opt_id')
                   ->where('style_id', '=', $style_id)
                   ->groupBy('costing.color_type_id')
                   ->get();

      $arr['colour_type'] = $colour_type;

      if($arr == null)
          throw new ModelNotFoundException("Requested section not found", 1);
      else
          return response([ 'data' => $arr ]);

    }

    public function change_style_colour(Request $request){

    $style_id  = $request->style_id;
    $season_id = $request->season_id;
    $stage_id  = $request->stage_id;
    $color_t   = $request->color_t;

    $st_colour = Costing::select('org_color.color_id', 'org_color.color_code')
                 ->join('costing_finish_goods', 'costing.id', '=', 'costing_finish_goods.costing_id')
                 ->join('org_color', 'costing_finish_goods.combo_color_id', '=', 'org_color.color_id')
                 ->where('style_id', '=', $style_id)
                 ->where('bom_stage_id', '=', $stage_id)
                 ->where('season_id', '=', $season_id)
                 ->where('color_type_id', '=', $color_t)
                 ->get();

    $arr['style_colour']  = $st_colour;

    $fob = Costing::select('fob')
    ->where('style_id', '=', $style_id)
    ->where('bom_stage_id', '=', $stage_id)
    ->where('season_id', '=', $season_id)
    ->where('color_type_id', '=', $color_t)
    ->get();

    $arr['fob']  = $fob;

    if($arr == null)
      throw new ModelNotFoundException("Requested section not found", 1);
    else
      return response([ 'data' => $arr ]);

    }


}
