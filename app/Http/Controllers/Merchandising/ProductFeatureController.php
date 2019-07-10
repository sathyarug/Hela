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
use App\Models\Merchandising\ProductFeatureComponent;
use App\Models\Merchandising\ProductSilhouette;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCategoryResource;
use App\Libraries\AppAuthorize;
use App\Models\Merchandising\ProductComponent;
use Illuminate\Support\Facades\DB;


class ProductFeatureController extends Controller
{

    var $authorize = null;

    public function loadProductFeature(Request $request) {
//        print_r('sss');exit;
        try{
//            echo json_encode(ProductCategory::all());
            echo json_encode(productFeature::where('product_feature_description', 'LIKE', '%'.$request->search.'%')
            ->where('status',1)->get());
//            return ProductCategoryResource::collection(ProductCategory::where('prod_cat_description', 'LIKE', '%'.$request->search.'%')->get() );
        }
        catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
//        $customer_list = Customer::all();
//        echo json_encode($customer_list);
    }

    private function get_next_line_no($po)
      {
        $max_no = PoOrderDetails::where('po_no','=',$po)->max('line_no');
  	  if($max_no == NULL){ $max_no= 0;}
        return ($max_no + 1);
      }


    public function save_product_feature(Request $request){
      $lines = $request->lines;
      //print_r($lines);
      //die();

      if($lines != null && sizeof($lines) >= 1){

        for($r = 0 ; $r < sizeof($lines) ; $r++)
        {
            if(isset($lines[$r]['product_silhouette_description']) == '')
              {
                $line_id = $r+1;
                $err = 'Silhouette Line '.$line_id.' cannot be empty.';
                return response([ 'data' => ['status' => 'error','message' => $err]]);

              }

        }

        $max_f = DB::table('product_feature')->max('product_feature_id');
        $max_f_n = $max_f + 1;
        $a = 1;
        for($x = 0 ; $x < sizeof($lines) ; $x++) {

        if(isset($lines[$x]['emb']) == 1){ $emblishment = 1; }else { $emblishment = 0; }
        if(isset($lines[$x]['wash']) == 1){ $washing = 1; }else{ $washing= 0; }
        if(isset($lines[$x]['display'])== ''){$dis = '';}else{ $dis = $lines[$x]['display']; }

        $silhouette = ProductSilhouette::select('*')
        ->where('product_silhouette_description','=',$lines[$x]['product_silhouette_description'])
        ->first();

        $PFC = new ProductFeatureComponent();
        $PFC->product_feature_id = $max_f_n;
        $PFC->product_component_id = $lines[$x]['pro_com_id'];
        $PFC->product_silhouette_id = $silhouette->product_silhouette_id;
        $PFC->line_no = $a ;
        $PFC->display_name = $dis;
        $PFC->emblishment = $emblishment;
        $PFC->washing = $washing;
        $PFC->status = 1;
        $PFC->save();
        $a++;
        }

        $pfc_list= ProductFeatureComponent::select(DB::raw('Count(product_component.product_component_description) as Count'),'product_component.product_component_description')
        ->join('product_component','product_feature_component.product_component_id','=','product_component.product_component_id')
        ->where('product_feature_component.product_feature_id','=',$max_f_n)
        ->groupBy('product_feature_component.product_component_id')
        ->get();

        $f = '';$a=array();
        for($y = 0 ; $y < sizeof($pfc_list) ; $y++) {
          $d = $pfc_list[$y]->Count;
          $e = $pfc_list[$y]->product_component_description;
          $f = $d.' '.$e;
          array_push($a,$f);
        }

        $separated = implode(" | ", $a);

        $PF = new productFeature();
        $PF ->product_feature_id = $max_f_n;
        $PF ->product_feature_description = strtoupper($separated);
        $PF ->status = 1;
        $PF ->count = sizeof($lines);
        $PF ->save();

        return response([
          'data' => [
            'status' => 'success',
            'message' => 'Saved successfully.',
            'max_f' => $max_f_n,
            'max_f_d' => strtoupper($separated)
          ]
        ] , 200);

      }

    }

    public function pro_listload_edit(Request $request){
      $id = $request->id;

      $subCat = ProductFeatureComponent::select('product_component.product_component_description AS assign','product_feature_component.display_name AS display','product_silhouette.product_silhouette_description','product_feature_component.emblishment AS emb','product_feature_component.washing AS wash','product_feature_component.feature_component_id','product_feature_component.product_component_id AS pro_com_id')
         ->join('product_component','product_feature_component.product_component_id','=','product_component.product_component_id')
         ->leftjoin('product_silhouette','product_feature_component.product_silhouette_id','=','product_silhouette.product_silhouette_id')
         ->where('product_feature_id' , '=', $id )
         ->where('product_feature_component.status' , '<>', 0 )
         ->get();

      $subCat1 = ProductComponent::select('*')
         ->where('status' , '<>', 0 )
         ->get();

      return response([ 'count'   => sizeof($subCat), 'subCat'=> $subCat,
                        'count2'  => sizeof($subCat1), 'subCat2'=> $subCat1 ]);

    }

    public function destroy($id)
    {
      $pro_f = ProductFeatureComponent::where('feature_component_id', $id)->update(['status' => 0]);
      return response([
        'data' => [
          'message' => 'Product Feature deactivated successfully.',
          'prod_f' => $pro_f
        ]
      ]);

    }

    public function update_product_feature(Request $request){
      $lines = $request->lines;
      $fe_data = $request->fe_data;
      //print_r($lines) ;
      //die();

      if($lines != null && sizeof($lines) >= 1){

          for($x = 0 ; $x < sizeof($lines) ; $x++) {

          if(isset($lines[$x]['emb']) == 1){ $emblishment = 1; }else { $emblishment = 0; }
          if(isset($lines[$x]['wash']) == 1){ $washing = 1; }else { $washing= 0; }

          if($lines[$x]['product_silhouette_description'] == '')
          {
            $line_id = $x+1;
            $err = 'Silhouette Line '.$line_id.' cannot be empty.';
            //return ['status' => 'error','message' => $err];
            return response([ 'data' => ['status' => 'error','message' => $err]]);
          }

          $silhouette = ProductSilhouette::select('*')
          ->where('product_silhouette_description','=',$lines[$x]['product_silhouette_description'])
          ->first();

          $PF = ProductFeatureComponent::find($lines[$x]['feature_component_id']);
          $PF->product_silhouette_id = $silhouette->product_silhouette_id;
          $PF->product_component_id = $lines[$x]['pro_com_id'];
          $PF->display_name = strtoupper($lines[$x]['display']);
          $PF->emblishment = $lines[$x]['emb'];
          $PF->washing = $lines[$x]['wash'];
          $PF->save();

          }


          $pfc_list= ProductFeatureComponent::select(DB::raw('Count(product_component.product_component_description) as Count'),'product_component.product_component_description')
          ->join('product_component','product_feature_component.product_component_id','=','product_component.product_component_id')
          ->where('product_feature_component.product_feature_id','=',$fe_data)
          ->where('product_feature_component.status' , '<>', 0 )
          ->groupBy('product_feature_component.product_component_id')
          ->get();

          $f = '';$a=array();
          for($y = 0 ; $y < sizeof($pfc_list) ; $y++) {
            $d = $pfc_list[$y]->Count;
            $e = $pfc_list[$y]->product_component_description;
            $f = $d.' '.$e;
            array_push($a,$f);
          }

          $separated = implode(" | ", $a);

          //$PF = new productFeature();
          $PF = productFeature::find($fe_data);
          $PF ->product_feature_description = strtoupper($separated);
          $PF ->count = sizeof($lines);
          $PF ->save();


        return response([ 'data' => [
          'message' => 'Product Feature updated successfully',
          'prod_f' => $PF,
          'max_f' => $fe_data,
          'max_f_d' => strtoupper($separated)
        ]]);



      }


    }

    public function save_line_fe(Request $request){

      $assign = $request->assign;
      $pro_com_id = $request->pro_com_id;
      $fe_data = $request->fe_data;

      $max_line_id = ProductFeatureComponent::where('product_feature_id','=',$fe_data)->max('line_no');

      $PF = new ProductFeatureComponent();
      $PF->product_feature_id = $fe_data;
      $PF->product_component_id = $pro_com_id;
      $PF->line_no = $max_line_id + 1;
      $PF->emblishment = 0;
      $PF->washing = 0;
      $PF->saveOrFail();

      //echo $PF->feature_component_id;


      $subCat = ProductFeatureComponent::select('product_component.product_component_description AS assign','product_feature_component.feature_component_id','product_feature_component.product_component_id AS pro_com_id','product_feature_component.emblishment AS emb','product_feature_component.washing AS wash')
         ->join('product_component','product_feature_component.product_component_id','=','product_component.product_component_id')
         ->where('feature_component_id' , '=', $PF->feature_component_id )
         ->get();

      return response([ 'count'   => sizeof($subCat), 'subCat'=> $subCat]);

    }





}
