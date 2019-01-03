<?php

namespace App\Http\Controllers\Merchandising;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Merchandising\styleCreation;
use App\Models\Org\Customer;
use App\Models\Org\Division;
use App\Models\Merchandising\productFeature;
use App\Models\Merchandising\ProductSilhouette;
use App\Models\Merchandising\ProductCategory;
use App\Models\Merchandising\productType;
use App\Models\Merchandising\StyleProductFeature;
use DB;
//use Illuminate\Http\Response;

class StyleCreationController extends Controller
{
    public function __construct()
    {
        //add functions names to 'except' paramert to skip authentication
        $this->middleware('jwt.verify', ['except' => ['index', 'loadStyles','GetStyleDetails']]);
    }

    //get customer list
    public function index(Request $request)
    {
        $type = $request->type;
        if($type == 'datatable')   {
            $data = $request->all();
            return response($this->datatable_search($data));
        }else{
            try{
                echo json_encode(styleCreation::where('style_no', 'LIKE', '%'.$request->search.'%')->get());
            }
            catch (JWTException $e) {
                // something went wrong whilst attempting to encode the token
                return response()->json(['error' => 'could_not_create_token'], 500);
            }

        }

    }

    private function datatable_search($data)
    {
        $start = $data['start'];
        $length = $data['length'];
        $draw = $data['draw'];
        $search = $data['search']['value'];
        $order = $data['order'][0];
        $order_column = $data['columns'][$order['column']]['data'];
        $order_type = $order['dir'];

        $section_list = styleCreation::select('*')
            ->where('style_no'  , 'like', $search.'%' )
            ->orWhere('style_description'  , 'like', $search.'%' )
            ->orderBy('status',$order_column, $order_type)
            ->offset($start)->limit($length)->get();

        $section_count = styleCreation::where('style_no'  , 'like', $search.'%' )
            ->orWhere('style_description'  , 'like', $search.'%' )
            ->count();

        return [
            "draw" => $draw,
            "recordsTotal" => $section_count,
            "recordsFiltered" => $section_count,
            "data" => $section_list
        ];
    }

    public function saveStyleCreation(Request $request) {
//        $payload = $request->avatar;
        if($request->style_id != null){
            $styleCreation = styleCreation::find($request->style_id);
        }else{
            $styleCreation = new styleCreation();
        }
        // echo "hello"; exit;


        if ($styleCreation->validate($request->all())) {

            $styleCreation->style_no =$request->style_no;
            // $styleCreation->product_feature_id =$request->ProductFeature['product_feature_id'];
            $styleCreation->product_category_id =$request->ProductCategory['prod_cat_id'];
            $styleCreation->product_silhouette_id =$request->ProductSilhouette['product_silhouette_id'];
            $styleCreation->customer_id =$request->customer['customer_id'];
            $styleCreation->pack_type_id =$request->ProductType['pack_type_id'];
            $styleCreation->division_id =$request->division['division_id'];
            $styleCreation->style_description =$request->style_description;
            $styleCreation->remark =$request->Remarks;

           // $styleCreation->image =$request->avatar['filename'];

             $styleCreation->saveOrFail();
            $styleCreationUpdate = styleCreation::find($styleCreation->style_id);

            $styleCreationUpdate->image =$styleCreation->style_id.'.png';
            $styleCreationUpdate->save();
//            dd($request->avatarHidden );
//            print_r($styleCreation->style_id);exit;
  // dd($request->ProductSilhouette['product_silhouette_id']);
            if($request->avatarHidden !=null){
                $this->saveImage($request->avatar['value'],$styleCreation->style_id);
            }
            $insertedId = $styleCreation->style_id;

            DB::table('style_product_feature')->where('style_id', '=', $insertedId)->delete();
    				$product_features = $request->get('ProductFeature');
    				$save_product_features = array();
    				if($product_features != '') {
    		  		foreach($product_features as $product_feature)		{
    						array_push($save_product_features,productFeature::find($product_feature['product_feature_id']));
    					}
    				}
    				$styleCreation->productFeature()->saveMany($save_product_features);
          //  $this->saveImage($request->avatar['value'],$styleCreation->style_id);
            echo json_encode(array('status' => 'success', 'message' => 'Customer details saved successfully.','image' =>$styleCreation->style_id.'.png'));
        } else {
            // failure, get errors
            $errors = $styleCreation->errors();
            echo json_encode(array('status' => 'error', 'message' => $errors));
        }
    }

    private function saveImage($image,$id){

        // your base64 encoded
        if (!file_exists(public_path().'/assets/styleImage')) {
            mkdir(public_path().'/assets/styleImage', 0777, true);
        }

        if (file_exists(public_path().'/assets/styleImage/'.$id.'.png')) {
//            dd(public_path().'/assets/styleImage/'.$image);
            rename(public_path().'/assets/styleImage/'.$id.'.png', public_path().'/assets/styleImage/'.strtotime("now").'_'.$id.'.png');
        }
//dd($id);
        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $imageName = $id.'.'.'png';
        \File::put(public_path().'/assets/styleImage/'.$imageName, base64_decode($image));
        return true;
    }
    
    public function loadStyles(){
        $style_list = styleCreation::all();
        echo json_encode($style_list);
    }
    
    public function GetStyleDetails(Request $request){       
        $style_details = styleCreation::GetStyleDetails($request->style_id);
        echo json_encode($style_details);
        
    }

    //get a Section
    public function show($id)
    {
        $style = styleCreation::with(['productFeature'])->find($id);

        $customer = Customer::find($style['customer_id']);
        // $productFeature = DB::table('style_product_feature')
        //           ->join('product_feature', 'style_product_feature.product_feature_id', '=', 'product_feature.product_feature_id')
        //           ->select('style_product_feature.id AS product_feature_id','product_feature.product_feature_description')
        //           ->where('style_product_feature.id','=',$style['style_id'])
        //           ->get();
        $ProductSilhouette = ProductSilhouette::find($style['product_silhouette_id']);
        $ProductCategory = ProductCategory::find($style['product_category_id']);
        $productType = productType::find($style['pack_type_id']);
        $divisions=DB::table('org_customer_divisions')
                  ->join('cust_division', 'org_customer_divisions.division_id', '=', 'cust_division.division_id')
                  ->select('org_customer_divisions.id AS division_id','cust_division.division_code')
                  ->where('org_customer_divisions.id','=',$style['division_id'])
                  ->get();
        // $avatarHidden = null;


// dd($productFeature);
        $style['customer']=$customer;
        // $style['product_feature']=$productFeature;
        $style['ProductSilhouette']=$ProductSilhouette;
        $style['ProductCategory']=$ProductCategory;
        $style['productType']=$productType;
        $style['division']=$divisions;
        // $style['image']=$avatarHidden;



//        dd($style);
//
//        foreach ($section AS $key=>$val){
//            dd($val);
//            //Customer::where('customer_id', '=', $request->search)->get()
//        }
        if($style == null)
            throw new ModelNotFoundException("Requested section not found", 1);
        else
            return response([ 'data' => $style ]);
    }

    //deactivate a style
    public function destroy($id)
    {
        $style = styleCreation::where('style_id', $id)->update(['status' => 0]);
        return response([
            'data' => [
                'message' => 'Style was deactivated successfully.',
                'style' => $style
            ]
        ] , Response::HTTP_NO_CONTENT);
    }

}
