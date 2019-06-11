<?php
/**
 * Created by PhpStorm.
 * User: shanilad
 * Date: 9/5/2018
 * Time: 4:10 PM
 */

namespace App\Http\Controllers\Merchandising;

use Illuminate\Http\Request;
use App\Models\Merchandising\productFeature;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCategoryResource;
use App\Libraries\AppAuthorize;


class ProductFeatureController extends Controller
{

    var $authorize = null;
    
    public function loadProductFeature(Request $request) {
//        print_r('sss');exit;
        try{
//            echo json_encode(ProductCategory::all());
            echo json_encode(productFeature::where('product_feature_description', 'LIKE', '%'.$request->search.'%')->where('status',1)->get());
//            return ProductCategoryResource::collection(ProductCategory::where('prod_cat_description', 'LIKE', '%'.$request->search.'%')->get() );
        }
        catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
//        $customer_list = Customer::all();
//        echo json_encode($customer_list);
    }
}
