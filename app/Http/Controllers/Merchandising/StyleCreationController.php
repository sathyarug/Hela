<?php

namespace App\Http\Controllers\Merchandising;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Merchandising\styleCreation;


class StyleCreationController extends Controller
{
    public function __construct()
    {
        //add functions names to 'except' paramert to skip authentication
        $this->middleware('jwt.verify', ['except' => ['index', 'loadStyles','GetStyleDetails']]);
    }

    //get customer list
    public function index(Request $request)
    {//print_r('eeee');exit;
        try{
            echo json_encode(styleCreation::where('style_no', 'LIKE', '%'.$request->search.'%')->get());
        }
        catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
    }

    public function saveStyleCreation(Request $request) {
//        $payload = $request->avatar;

        $styleCreation = new styleCreation();
        if ($styleCreation->validate($request->all())) {

            $styleCreation->style_no =$request->style_no;
            $styleCreation->product_feature_id =$request->ProductFeature['product_feature_id'];
            $styleCreation->product_category_id =$request->ProductCategory['prod_cat_id'];
            $styleCreation->product_silhouette_id =$request->ProductSilhouette['product_silhouette_id'];
            $styleCreation->customer_id =$request->customer['customer_id'];
            $styleCreation->pack_type_id =$request->ProductType['pack_type_id'];
//            $styleCreation->division_id =$request->ProductType['pack_type_id'];
            $styleCreation->style_description =$request->style_description;
            $styleCreation->remark =$request->Remarks;

           // $styleCreation->image =$request->avatar['filename'];

             $styleCreation->saveOrFail();
            $styleCreationUpdate = styleCreation::find($styleCreation->style_id);

            $styleCreationUpdate->image =$styleCreation->style_id.'.png';
            $styleCreationUpdate->save();
//            print_r($styleCreation->style_id);exit;
            $this->saveImage($request->avatar['value'],$styleCreation->style_id);
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

}
