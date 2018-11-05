<?php

namespace App\Http\Controllers\Finance;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use App\Models\Finance\Transaction ;

class TransactionController extends Controller
{
    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
    }

    //get shipment term list
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
        $transaction = new Transaction();
        $transaction->fill($request->all());
        $transaction->status = 1;
        $transaction->save();

        return response([ 'data' => [
          'message' => ' Transaction saved successfully',
          'transaction' => $transaction
          ]
        ], Response::HTTP_CREATED );
    }

    //get shipment term
    public function show($id)
    {
        $transaction = Transaction::find($id);
        if($transaction == null)
          throw new ModelNotFoundException("Requested shipment term not found", 1);
        else
          return response([ 'data' => $transaction ]);
    }


    //update a shipment term
    public function update(Request $request, $id)
    {
        $transaction = Transaction::find($id);
        $transaction->fill($request->except('trans_code'));
        $transaction->save();

        return response([ 'data' => [
          'message' => 'Transaction updated successfully',
          'transaction' => $transaction
        ]]);

    }

    //deactivate a ship term
    public function destroy($id)
    {
        $transaction = Transaction::where('trans_id', $id)->update(['status' => 0]);
        return response([
          'data' => [
            'message' => 'Shipment term was deactivated successfully.',
            'transaction' => $transaction
          ]
        ] , Response::HTTP_NO_CONTENT);
    }


    //validate anything based on requirements
    public function validate_data(Request $request){
      $for = $request->for;
      if($for == 'duplicate')
      {
        return response($this->validate_duplicate_code($request->trans_id , $request->trans_code));
      }
    }


    //check shipment cterm code code already exists
    private function validate_duplicate_code($id , $code)
    {
       $transaction =  Transaction::where('trans_code','=',$code)->first();
      if( $transaction == null){
        return ['status' => 'success'];
      }
      else if( $transaction->trans_id == $id){
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
        $query = Transaction::select('*');
      }
      else{
        $fields = explode(',', $fields);
        $query = Transaction::select($fields);
        if($active != null && $active != ''){
          $query->where([['status', '=', $active]]);
        }
      }
      return $query->get();
    }


    //search shipment terms for autocomplete
    private function autocomplete_search($search)
  	{
  		$transaction_lists = Transaction::select('trans_id','trans_code')
  		->where([['trans_code', 'like', '%' . $search . '%'],]) ->get();
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

      $transaction_list = Transaction::select('*')
      ->where('trans_code'  , 'like', $search.'%' )
      ->orWhere('trans_description'  , 'like', $search.'%' )
      ->orderBy($order_column, $order_type)
      ->offset($start)->limit($length)->get();

      $transaction_count = Transaction::where('trans_code'  , 'like', $search.'%' )
      ->orWhere('trans_description'  , 'like', $search.'%' )
      ->count();

      return [
          "draw" => $draw,
          "recordsTotal" => $transaction_count,
          "recordsFiltered" => $transaction_count,
          "data" => $transaction_list
      ];
    }

}
