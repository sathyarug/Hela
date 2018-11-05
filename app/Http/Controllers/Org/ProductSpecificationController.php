<?php

namespace App\Http\Controllers\Org;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use App\Models\Org\ProductSpecification  ;

class  ProductSpecificationController extends Controller
{
    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
    }

    //get product specification listerm list
    public function index(Request $request)
    {
      $type = $request->type;

      if($type == 'datatable')   {
        $data = $request->all();
        return response($this->datatable_search($data));
      }
      else if($type == 'auto')    {
        $search = $request->search;
        return response($this->autocomplete_search($search));
      }
      else{
        $active = $request->active;
        $fields = $request->fields;
        return response([
          'data' => $this->list($active , $fields)
        ]);
      }
    }

    //create a shipment term
    public function store(Request $request)
    {
        $productSpecification= new  ProductSpecification ();
        $productSpecification->fill($request->all());
        $productSpecification->status = 1;
        $productSpecification->save();

        return response([ 'data' => [
          'message' => ' Product Specification saved successfully',
          'productSpecification' => $productSpecification
          ]
        ], Response::HTTP_CREATED );
    }

    //get shipment term
    public function show($id)
    {
        $productSpecification = ProductSpecification::find($id);
        if($productSpecification == null)
          throw new ModelNotFoundException("Requested shipment term not found", 1);
        else
          return response([ 'data' => $productSpecification ]);
    }


    //update a shipment term
    public function update(Request $request, $id)
    {
        $productSpecification =  ProductSpecification::find($id);
        $productSpecification->fill($request->all());
        $productSpecification->save();

        return response([ 'data' => [
          'message' => ' Product Specification updated successfully',
          'transaction' => $productSpecification
        ]]);

    }



    //deactivate a ship term
    public function destroy($id)
    {
        $productSpecification =ProductSpecification::where('prod_cat_id', $id)->update(['status' => 0]);
        return response([
          'data' => [
            'message' => 'Shipment term was deactivated successfully.',
            'transaction' => $productSpecification
          ]
        ] , Response::HTTP_NO_CONTENT);
    }


    //validate anything based on requirements
    public function validate_data(Request $request){
      $for = $request->for;
      if($for == 'duplicate')
      {
        return response($this->validate_duplicate_code($request->prod_cat_id , $request->prod_cat_description));
      }
    }

//------not edited
    //check shipment cterm code code already exists
    private function validate_duplicate_code($id , $code)
    {
       $productSpecification =ProductSpecification::where('prod_cat_id','=',$code)->first();
      if( $productSpecification  == null){
        return ['status' => 'success'];
      }
      else if( $productSpecification ->prod_cat_id == $id){
        return ['status' => 'success'];
      }
      else {
        return ['status' => 'error','message' => 'Ship term code already exists'];
      }
    }


    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
      $query = null;
      if($fields == null || $fields == '') {
        $query = ProductSpecification::select('*');
      }
      else{
        $fields = explode(',', $fields);
        $query = ProductSpecification::select($fields);
        if($active != null && $active != ''){
          $query->where([['status', '=', $active]]);
        }
      }
      return $query->get();
    }


    //search shipment terms for autocomplete
    private function autocomplete_search($search)
  	{
  		$transaction_lists = ProductSpecification::select('prod_cat_description')
  		->where([['prod_cat_description', 'like', '%' . $search . '%'],]) ->get();
  		return $transaction_lists;
  	}


    //get searched ship terms for datatable plugin format
    private function datatable_search($data)
    {
      $start = $data['start'];
      $length = $data['length'];
      $draw = $data['draw'];
      $search = $data['search']['value'];
      $order = $data['order'][0];
      $order_column = $data['columns'][$order['column']]['data'];
      $order_type = $order['dir'];

      $transaction_list = ProductSpecification::select('*')
      ->where('prod_cat_description'  , 'like', $search.'%' )
      ->orderBy($order_column, $order_type)
      ->offset($start)->limit($length)->get();

      $transaction_count = ProductSpecification::where('prod_cat_description'  , 'like', $search.'%' )
      ->count();

      return [
          "draw" => $draw,
          "recordsTotal" => $transaction_count,
          "recordsFiltered" => $transaction_count,
          "data" => $transaction_list
      ];
    }

}
