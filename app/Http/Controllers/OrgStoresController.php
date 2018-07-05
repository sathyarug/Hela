<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\OrgStore;
use App\OrgLocation;
use App\Http\Controllers\Controller;

class OrgStoresController extends Controller
{
    
    
    
    public function postdata(Request $request)
   {
        $OrgStore = new OrgStore(); 
        
        if ($OrgStore->validate($request->all()))   
        {
            if($request->stores_hid > 0){
                $OrgStore = OrgStore::find($request->stores_hid);
            }     
            $OrgStore->fill($request->all());
           // print_r($OrgStore);
            $OrgStore->status= 1 ;
           // $OrgStore->loc_id = fac_location;
            $OrgStore->created_by = 1;  
            $result = $OrgStore->saveOrFail();
           // echo json_encode(array('Saved'));
            if($result)
              {
               // echo json_encode(["message"=>$result]);
                  return response()->json(["message"=>$result]);
              }
        }
        else
        {            
            // failure, get errors
            $errors = $OrgStore->errors();
            print_r($errors);
        }        


   }
   

        
        public function loaddata()
	{
          
            
		$stores_list = OrgStore::join('org_location', 'org_store.loc_id', '=', 'org_location.loc_id')
		->select('org_store.*', 'org_store.store_id','org_location.loc_code')
		->get();
		echo json_encode($stores_list);
                
	}
        
         public function edit(Request $request)
   {

        $store_id= $request->store_id;
       // $store = OrgStore::find($store_id);  
        
         $store = OrgStore::join('org_location', 'org_store.loc_id', '=', 'org_location.loc_id')
		->select('org_store.*', 'org_store.store_id','org_location.company_code')
                ->where('org_store.store_id', '=', $store_id)
		->get();
        
        
        
        echo json_encode($store);
   			
   }
   
    public function delete(Request $request)
   {
        $store_id = $request->store_id;
        //$source = Main_Source::find($source_id);
        //$source->delete();
        $store = OrgStore::where('store_id', $store_id)->update(['status' => 0]);
        echo json_encode(array('delete'));
   }
   
   
    public function check_Store_Name(Request $request)
   {


   	$count = OrgStore::where('store_name','=',$request->code)->count();

        if($request->idcode > 0){

          $user = OrgStore::where('store_id', $request->idcode)->first();

              if($user->store_name == $request->code)
              {
                  $msg = true;

              }else{

                  $msg = 'Already exists. please try another one';

              }


        }else{

            if($count == 1){ 

                  $msg = 'Already exists. please try another one'; 

              }else{ 

                  $msg = true; 
                  
              }

        }
   			echo json_encode($msg);
   }


    public function load_fac_locations(Request $request){

            $search_c = $request->search;
           // print_r($search_c);
            $fac_lists = OrgLocation::select('loc_id','loc_code','company_code')
            ->where([['company_code', 'like', '%' . $search_c . '%'],]) ->get();


            return response()->json(['items'=>$fac_lists]);
            //return $select_source;

    }
    
    public function load_fac_section(Request $request){

            $search_c = $request->search;
           // print_r($search_c);
            $fac_lists = OrgLocation::select('loc_id','loc_code','company_code')
            ->where([['company_code', 'like', '%' . $search_c . '%'],]) ->get();


            return response()->json(['items'=>$fac_lists]);
            //return $select_source;

    }
                    
	


}