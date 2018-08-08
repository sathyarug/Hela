<?php

namespace App\Http\Controllers\Org\Location;

use Illuminate\Http\Request;
use App\Models\Org\Location\Source;
use App\Http\Controllers\Controller;

class SourceController extends Controller
{

  //save or update a source
  public function save(Request $request)
  {
    $source = new Source();
    if ($source->validate($request->all()))
    {
      if($request->source_id > 0){
        $source = Source::find($request->source_id);
      }
      $source->fill($request->all());
      $source->status = 1;
      $source->created_by = 1;
      $result = $source->saveOrFail();

      echo json_encode(array('status' => 'success' , 'message' => 'Source details saved successfully.') );
    }
    else
    {
      // failure, get errors
      $errors = $source->errors();
      echo json_encode(array('status' => 'error' , 'message' => $errors));
    }
  }

  //get searched source list
  public function get_list(Request $request)
  {
      $data = $request->all();
      $start = $data['start'];
      $length = $data['length'];
      $draw = $data['draw'];
      $search = $data['search']['value'];
      $order = $data['order'][0];
      $order_column = $data['columns'][$order['column']]['data'];
      $order_type = $order['dir'];

      $source_list = Source::where('source_code','like',$search.'%')
      ->orWhere('source_name', 'like', $search.'%')
      ->orderBy($order_column, $order_type)
      ->offset($start)->limit($length)->get();
      $source_list_count = Source::where('source_code','like',$search.'%')
      ->orWhere('source_name', 'like', $search.'%')
      ->count();

      echo json_encode(array(
          "draw" => $draw,
          "recordsTotal" => $source_list_count,
          "recordsFiltered" => $source_list_count,
          "data" => $source_list
      ));
 }

 //check a source code already exists
 public function check_code(Request $request)
 {
   $source = Source::where('source_code','=',$request->source_code)->first();
   if($source == null){
     echo json_encode(array('status' => 'success'));
   }
   else if($source->source_id == $request->source_id){
     echo json_encode(array('status' => 'success'));
   }
   else {
     echo json_encode(array('status' => 'error','message' => 'Source code already exists'));
   }
 }


public function edit(Request $request)
{
   $source_id = $request->source_id;
   $source = Source::find($source_id);
   echo json_encode($source);
}


public function change_status(Request $request)
{
    $source_id = $request->source_id;
    $source = Source::where('source_id', $source_id)->update(['status' => 0]);
    echo json_encode(array(
      'status' => 'success',
      'message' => 'Source deactivated successfully'
    ));
}

//get only active source list
public function get_active_source_list(Request $request)
{
  $search_c = $request->search;
  $s_source_lists = Source::select('source_id','source_code','source_name')
  ->where([
    ['status', '=', '1']
    /*['source_name', 'like', '%' . $search_c . '%'],*/
  ])
  ->get();
  return response()->json($s_source_lists);
}



}
