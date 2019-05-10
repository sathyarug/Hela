<?php
/**
 * Created by PhpStorm.
 * User: sankap
 * Date: 5/7/2019
 * Time: 11:47 PM
 */

namespace App\Models\Store;
use App\BaseValidator;


class ReturnToStoresDetails extends BaseValidator
{
    protected $table='store_return_to_stores_detail';
    protected $primaryKey='id';
   // const UPDATED_AT='updated_date';
   // const CREATED_AT='created_date';
    public $timestamps = false;

    protected $fillable=['return_id','so_no', 'cus_po', 'item_code','material_code',  'color', 'size','uom', 'grn_no', 'qty', 'issue_id'];

    protected $rules=array(
        ////'color_code'=>'required',
        //'color_name'=>'required'
    );

    public function returnHeader(){
        return $this->hasMany('App\Models\Store\ReturnToStoresHeader', 'return_id', 'return_id');
    }

    public static function boot(){}

    public function __construct() {
        parent::__construct();
    }
}