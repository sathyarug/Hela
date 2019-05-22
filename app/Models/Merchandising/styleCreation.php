<?php

/**
 * Created by PhpStorm.
 * User: shanilad
 * Date: 9/10/2018
 * Time: 4:55 PM
 */

namespace App\Models\Merchandising;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\BaseValidator;

class styleCreation extends BaseValidator {

    protected $table = 'style_creation';
    protected $primaryKey = 'style_id';

    const UPDATED_AT = 'updated_date';
    const CREATED_AT = 'created_date';

//    protected $fillable = ['pack_type_description'];
//    protected $rules = array(
//        'pack_type_description' => 'required'
//
//    );



    public function __construct() {
        parent::__construct();
        $this->attributes = array(
            'updated_by' => 2//Session::get("user_id")
        );
    }

    public function scopeGetStyleDetails($style_id) {
        $aa = DB::table('style_creation')
                        ->join('prod_category', 'prod_category.prod_cat_id', '=', 'style_creation.product_category_id')
                        ->join('cust_customer', 'cust_customer.customer_id', '=', 'style_creation.customer_id')
                        ->join('product_feature', 'product_feature.product_feature_id', '=', 'style_creation.product_feature_id')
                        ->join('product_silhouette', 'product_silhouette.product_silhouette_id', '=', 'style_creation.product_silhouette_id')
                        ->join('cust_division', 'cust_division.division_id', '=', 'style_creation.division_id')
                        ->select('style_creation.style_description', 'prod_category.prod_cat_description', 'cust_customer.customer_name', 'product_feature.product_feature_description', 'product_silhouette.product_silhouette_description', 'cust_division.division_description', 'style_creation.image')
                        ->where('style_creation.style_id','=',$style_id)
                        ->toSql();
                        //->get();
                        dd($aa);
    }

    //default currency of the company
    public function currency() {
        return $this->belongsTo('App\Models\Finance\Customer', 'customer_id');
    }

    //Style Product Feature
    public function productFeature()
    {
        return $this->belongsToMany('App\Models\Merchandising\productFeature','style_product_feature','style_id','product_feature_id')
        ->withPivot('id');
    }

    public function customer() {
        return $this->belongsTo('App\Models\Org\Customer', 'customer_id');
    }

    public function division() {
        return $this->belongsTo('App\Models\Org\Division', 'division_id');
    }

    public function productType() {
        return $this->belongsTo('App\Models\Org\ProductType', 'pack_type_id');
    }

}
