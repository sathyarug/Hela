<?php

namespace App\Http\Controllers\Org;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Http\Controllers\Controller;
use App\Models\Org\SilhouetteClassification;

class  SilhouetteClassificationController extends Controller
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
        $silhouetteClassification= new  SilhouetteClassification ();
        $silhouetteClassification->fill($request->all());
        $silhouetteClassification->status = 1;
        $silhouetteClassification->save();

        return response([ 'data' => [
          'message' => ' Silhouette Classification saved successfully',
          'silhouetteClassification' => $silhouetteClassification
          ]
        ], Response::HTTP_CREATED );
    }

    //get shipment term
    public function show($id)
    {
        $silhouetteClassification = SilhouetteClassification::find($id);
        if($silhouetteClassification== null)
          throw new ModelNotFoundException("Requested Silhouette Classification not found", 1);
        else
          return response([ 'data' => $silhouetteClassification]);
    }


    //update a shipment term
    public function update(Request $request, $id)
    {
        $silhouetteClassification =  SilhouetteClassification::find($id);
        $silhouetteClassification->fill($request->all());
        $silhouetteClassification->save();

        return response([ 'data' => [
          'message' => ' Silhouette Classification updated successfully',
          'silhouetteClassification' => $silhouetteClassification
        ]]);

    }



    //deactivate a ship term
    public function destroy($id)
    {
        $silhouetteClassification =SilhouetteClassification::where('sil_class_id', $id)->update(['status' => 0]);
        return response([
          'data' => [
            'message' => 'Silhouette Classification was deactivated successfully.',
            'silhouetteClassification' => $silhouetteClassification
          ]
        ] , Response::HTTP_NO_CONTENT);
    }


    //validate anything based on requirements
    public function validate_data(Request $request){
      $for = $request->for;
      if($for == 'duplicate')
      {
        return response($this->validate_duplicate_code($request->sil_class_id, $request->sil_class_description));
      }
    }


    //check silhouetteClassification already exists
    private function validate_duplicate_code($id , $code)
    {
       $silhouetteClassification =SilhouetteClassification::where([['sil_class_description','=',$code]])->first();

       if($silhouetteClassification == null){
       echo json_encode(array('status' => 'success'));
       }
       else if($silhouetteClassification->sil_class_id== $id){
       echo json_encode(array('status' => 'success'));
       }
       else {
       echo json_encode(array('status' => 'error','message' => 'Record already exists'));
       }
    }


    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
      $query = null;
      if($fields == null || $fields == '') {
        $query = SilhouetteClassification::select('*');
      }
      else{
        $fields = explode(',', $fields);
        $query = SilhouetteClassification::select($fields);
        if($active != null && $active != ''){
          $query->where([['status', '=', $active]]);
        }
      }
      return $query->get();
    }


    //search shipment terms for autocomplete
    private function autocomplete_search($search)
  	{
  		$silhouetteClassification_lists = SilhouetteClassification::select('sil_class_description')
  		->where([['sil_class_description', 'like', '%' . $search . '%'],]) ->get();
  		return $silhouetteClassification_lists;
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

      $silhouetteClassification_lists =SilhouetteClassification::select('*')
      ->where('sil_class_description'  , 'like', $search.'%' )
      ->orderBy($order_column, $order_type)
      ->offset($start)->limit($length)->get();

      $silhouetteClassification_count = SilhouetteClassification::where('sil_class_description'  , 'like', $search.'%' )
      ->count();

      return [
          "draw" => $draw,
          "recordsTotal" => $silhouetteClassification_count,
          "recordsFiltered" => $silhouetteClassification_count,
          "data" =>   $silhouetteClassification_lists
      ];
    }

}
