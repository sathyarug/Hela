<?php

namespace App\Http\Controllers\Org;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use App\Models\Org\Silhouette;
use App\Models\Merchandising\StyleCreation;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Libraries\CapitalizeAllFields;
class SilhouetteController extends Controller
{
    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
    }

    //get Silhouette list
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


    //create a Silhouette
    public function store(Request $request)
    {
      $silhouette = new Silhouette();
      if($silhouette->validate($request->all()))
      {
        $silhouette->fill($request->all());
        $capitalizeAllFields=CapitalizeAllFields::setCapitalAll($silhouette);
        $silhouette->status = 1;
        $silhouette->save();

        return response([ 'data' => [
          'message' => 'Silhouette saved successfully',
          'silhouette' => $silhouette,
            'status'=>'1'
          ]
        ], Response::HTTP_CREATED );
      }
      else
      {
          $errors = $silhouette->errors();// failure, get errors
          return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }


    //get a Silhouette
    public function show($id)
    {
      $silhouette = Silhouette::find($id);
      if($silhouette == null)
        throw new ModelNotFoundException("Requested silhouette not found", 1);
      else
        return response([ 'data' => $silhouette ]);
    }


    //update a Silhouette
    public function update(Request $request, $id)
    {
      //$styleCreation=StyleCreation::where([['product_silhouette_id','=',$id]]);
      $is_exsits_in_style=DB::table('style_creation')->where('product_silhouette_id',$id)->exists();
      $silhouette = Silhouette::find($id);
      if($silhouette->validate($request->all()))
      {
        if($is_exsits_in_style==true){
          return response([ 'data' => [
            'message' => 'Product Silhouette Already in Use',
            'status'=>'0'
          ]]);
        }
        else if($is_exsits_in_style==false){
        $silhouette->fill($request->all());
        $capitalizeAllFields=CapitalizeAllFields::setCapitalAll($silhouette);
        $silhouette->save();

        return response([ 'data' => [
          'message' => 'Silhouette updated successfully',
          'silhouette' => $silhouette,
          'status'=>'1'
        ]]);
      }
    }
      else
      {
        $errors = $silhouette->errors();// failure, get errors
        return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }


    //deactivate a Silhouette
    public function destroy($id)
    {
      $styleCreation=StyleCreation::where([['product_silhouette_id','=',$id]]);
      $is_exsits_in_style=DB::table('style_creation')->where('product_silhouette_id',$id)->exists();
      if($is_exsits_in_style==true){
        return response([
          'data'=>[
            'message'=>'Product Silhouatte Alaredy in Use.',
            'status'=>'0'
          ]
        ]);
      }
      else {
      $silhouette = Silhouette::where('product_silhouette_id', $id)->update(['status' => 0]);

      return response([
        'data' => [
          'message' => 'Silhouette deactivated successfully.',
          'silhouette' => $silhouette,
          'status'=>'1'
        ]
      ]);
    }
    }


    //validate anything based on requirements
    public function validate_data(Request $request){
      $for = $request->for;
      if($for == 'duplicate')
      {
        return response($this->validate_duplicate_code($request->product_silhouette_id , $request->product_silhouette_description));
      }
    }


    //check Silhouette code already exists
    private function validate_duplicate_code($id , $code)
    {
      $silhouette = Silhouette::where('product_silhouette_description','=',$code)->first();
      if($silhouette == null){
        return ['status' => 'success'];
      }
      else if($silhouette->product_silhouette_id == $id){
        return ['status' => 'success'];
      }
      else {
        return ['status' => 'error','message' => 'Silhouette description already exists'];
      }
    }


    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
      $query = null;
      if($fields == null || $fields == '') {
        $query = Silhouette::select('*');
      }
      else{
        $fields = explode(',', $fields);
        $query = Silhouette::select($fields);
        if($active != null && $active != ''){
          $query->where([['status', '=', $active]]);
        }
      }
      return $query->get();
    }

    //search Silhouette for autocomplete
    private function autocomplete_search($search)
  	{
      $active=1;
  		$silhouette_lists = Silhouette::select('product_silhouette_id','product_silhouette_description')
  		->where([['product_silhouette_description', 'like', '%' . $search . '%']])
      ->where('status','=',$active)
      ->get();
  		return $silhouette_lists;
  	}


    //get searched Silhouette for datatable plugin format
    private function datatable_search($data)
    {
      $start = $data['start'];
      $length = $data['length'];
      $draw = $data['draw'];
      $search = $data['search']['value'];
      $order = $data['order'][0];
      $order_column = $data['columns'][$order['column']]['data'];
      $order_type = $order['dir'];

      $silhouette_list = Silhouette::select('*')
      ->where('product_silhouette_description'  , 'like', $search.'%' )
      ->orderBy($order_column, $order_type)
      ->offset($start)->limit($length)->get();

      $silhouette_count = Silhouette::where('product_silhouette_description'  , 'like', $search.'%' )
      ->count();

      return [
          "draw" => $draw,
          "recordsTotal" => $silhouette_count,
          "recordsFiltered" => $silhouette_count,
          "data" => $silhouette_list
      ];
    }

}
