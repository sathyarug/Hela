<?php

namespace App\Http\Controllers\Finance;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use App\Models\Finance\GoodsType;

class GoodsTypeController extends Controller
{
    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
    }

    //get goods type list
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
      else {
        $active = $request->active;
        $fields = $request->fields;
        return response([
          'data' => $this->list($active , $fields)
        ]);
      }
    }

    //create g goods type
    public function store(Request $request)
    {
        $goodsType = new GoodsType();
        $goodsType->goods_type_description = $request->goods_type_description;
        $goodsType->status = 1;
        $goodsType->save();

        return response([ 'data' => [
          'message' => 'Goods type saved successfully',
          'goodsType' => $goodsType
          ]
        ], Response::HTTP_CREATED );
    }

    //get goods type
    public function show($id)
    {
        $goodsType = GoodsType::find($id);
        if($goodsType == null)
          throw new ModelNotFoundException("Requested goods type not found", 1);
        else
          return response( ['data' => $goodsType] );
    }


    //update a goods type
    public function update(Request $request, $id)
    {
        $goodsType = GoodsType::find($id);
        $goodsType->goods_type_description = $request->goods_type_description;
        $goodsType->save();

        return response([ 'data' => [
          'message' => 'Goods type updated successfully',
          'goodsType' => $goodsType
        ]]);
    }

    //deactivate a goods type
    public function destroy($id)
    {
        $goodsType = GoodsType::where('goods_type_id', $id)->update(['status' => 0]);
        return response([
          'data' => [
            'message' => 'Goods type was deactivated successfully.',
            'goodsType' => $goodsType
          ]
        ] , Response::HTTP_NO_CONTENT);
    }


    //validate anything based on requirements
    public function validate_data(Request $request){
      $for = $request->for;
      if($for == 'duplicate')
      {
        return response($this->validate_duplicate_code($request->goods_type_id , $request->goods_type_description));
      }
    }


    //check goods type description already exists
    private function validate_duplicate_code($id , $code)
    {
      $goodsType = GoodsType::where('goods_type_description','=',$code)->first();
      if($goodsType == null){
        return ['status' => 'success'];
      }
      else if($goodsType->goods_type_id == $id){
        return ['status' => 'success'];
      }
      else {
        return ['status' => 'error','message' => 'Goods type description already exists'];
      }
    }


    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
      $query = null;
      if($fields == null || $fields == '') {
        $query = GoodsType::select('*');
      }
      else{
        $fields = explode(',', $fields);
        $query = GoodsType::select($fields);
        if($active != null && $active != ''){
          $query->where([['status', '=', $active]]);
        }
      }
      return $query->get();
    }


    //search goods types for autocomplete
    private function autocomplete_search($search)
  	{
  		$goods_type_lists = GoodsType::select('goods_type_id','goods_type_description')
  		->where([['goods_type_description', 'like', '%' . $search . '%'],]) ->get();
  		return $goods_type_lists;
  	}


    //get searched goods types for datatable plugin format
    private function datatable_search($data)
    {
      $start = $data['start'];
      $length = $data['length'];
      $draw = $data['draw'];
      $search = $data['search']['value'];
      $order = $data['order'][0];
      $order_column = $data['columns'][$order['column']]['data'];
      $order_type = $order['dir'];

      $goods_type_list = GoodsType::select('*')
      ->where('goods_type_description'  , 'like', $search.'%' )
      ->orderBy($order_column, $order_type)
      ->offset($start)->limit($length)->get();

      $goods_type_count = GoodsType::where('goods_type_description'  , 'like', $search.'%' )
      ->count();

      return [
          "draw" => $draw,
          "recordsTotal" => $goods_type_count,
          "recordsFiltered" => $goods_type_count,
          "data" => $goods_type_list
      ];
    }

}
