<?php

namespace App\Http\Controllers\Org\Location;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use App\Models\Org\Location\Company;
use App\Models\Org\Department;
use App\Models\Org\Section;

class CompanyController extends Controller
{
    public function __construct()
    {
      //add functions names to 'except' paramert to skip authentication
      $this->middleware('jwt.verify', ['except' => ['index']]);
    }

    //get Company list
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


    //create a Company
    public function store(Request $request)
    {
      $company = new Company();
      if($company->validate($request->all()))
      {
        $company->fill($request->all());
        $company->status = 1;
        $company->created_by = 1;
        $result = $company->saveOrFail();
        $insertedId = $company->company_id;

        /*DB::table('org_company_departments')->where('company_id', '=', $insertedId)->delete();
  			$departments = $request->get('departments');
  			$save_departments = array();
  			if($departments != '') {
  	  		foreach($departments as $dep)		{
  					array_push($save_departments,Department::find($dep['dep_id']));
  				}
  			}
  			$company->departments()->saveMany($save_departments);

        DB::table('org_company_sections')->where('company_id', '=', $insertedId)->delete();
  			$sections = $request->get('sections');
  			$save_sections = array();
  			if($sections != '') {
  	  		foreach($sections as $sec)		{
  					array_push($save_sections,Section::find($sec['section_id']));
  				}
  			}
  			$company->sections()->saveMany($save_sections);*/

        return response([ 'data' => [
          'message' => 'Company was saved successfully',
          'company' => $company
          ]
        ], Response::HTTP_CREATED );
      }
      else
      {
          $errors = $company->errors();// failure, get errors
          return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }


    //get a Company
    public function show($id)
    {
      $company = Company::find($id);
      if($company == null)
        throw new ModelNotFoundException("Requested company not found", 1);
      else
        return response([ 'data' => $company ]);
    }


    //update a Company
    public function update(Request $request, $id)
    {
      $company = Company::find($id);
      if($company->validate($request->all()))
      {
        $company->fill($request->except('company_code'));
        $company->save();

        return response([ 'data' => [
          'message' => 'Company was updated successfully',
          'company' => $company
        ]]);
      }
      else
      {
        $errors = $company->errors();// failure, get errors
        return response(['errors' => ['validationErrors' => $errors]], Response::HTTP_UNPROCESSABLE_ENTITY);
      }
    }


    //deactivate a Company
    public function destroy($id)
    {
      $company = Company::where('company_id', $id)->update(['status' => 0]);
      return response([
        'data' => [
          'message' => 'Company was deactivated successfully.',
          'company' => $company
        ]
      ] , Response::HTTP_NO_CONTENT);
    }


    //validate anything based on requirements
    public function validate_data(Request $request){
      $for = $request->for;
      if($for == 'duplicate')
      {
        return response($this->validate_duplicate_code($request->company_id , $request->company_code));
      }
    }


    //check Company code already exists
    private function validate_duplicate_code($id , $code)
    {
      $company = Company::where('company_code','=',$code)->first();
      if($company == null){
        return ['status' => 'success'];
      }
      else if($company->company_id == $id){
        return ['status' => 'success'];
      }
      else {
        return ['status' => 'error','message' => 'Company code already exists'];
      }
    }


    //get filtered fields only
    private function list($active = 0 , $fields = null)
    {
      $query = null;
      if($fields == null || $fields == '') {
        $query = Company::select('*');
      }
      else{
        $fields = explode(',', $fields);
        $query = Company::select($fields);
        if($active != null && $active != ''){
          $query->where([['status', '=', $active]]);
        }
      }
      return $query->get();
    }

    //search Company for autocomplete
    private function autocomplete_search($search)
  	{
  		$company_lists = Company::select('company_id','comapny_name')
  		->where([['comapny_name', 'like', '%' . $search . '%'],]) ->get();
  		return $company_lists;
  	}


    //get searched Companys for datatable plugin format
    private function datatable_search($data)
    {
      $start = $data['start'];
      $length = $data['length'];
      $draw = $data['draw'];
      $search = $data['search']['value'];
      $order = $data['order'][0];
      $order_column = $data['columns'][$order['column']]['data'];
      $order_type = $order['dir'];

      $company_list = Company::join('org_group', 'org_company.group_id', '=', 'org_group.group_id')
  		->select('org_company.*', 'org_group.group_name')
  		->where('company_code','like',$search.'%')
  		->orWhere('company_name', 'like', $search.'%')
  		->orWhere('group_name', 'like', $search.'%')
  		->orderBy($order_column, $order_type)
  		->offset($start)->limit($length)->get();

  		$company_count = Company::join('org_group', 'org_company.group_id', '=', 'org_group.group_id')
  		->select('org_company.*', 'org_group.group_name')
  		->where('company_code','like',$search.'%')
  		->orWhere('company_name', 'like', $search.'%')
  		->orWhere('group_name', 'like', $search.'%')
  		->count();

      return [
          "draw" => $draw,
          "recordsTotal" => $company_count,
          "recordsFiltered" => $company_count,
          "data" => $company_list
      ];
    }

}
