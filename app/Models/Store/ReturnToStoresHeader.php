<?php
/**
 * Created by PhpStorm.
 * User: sankap
 * Date: 5/7/2019
 * Time: 11:45 PM
 */

namespace App\Models\Store;
use App\BaseValidator;


class ReturnToStoresHeader extends BaseValidator
{
    protected $table='store_return_to_stores_header';
    protected $primaryKey='return_id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

    protected $fillable=['return_no','location', 'stores', 'created_by','updated_by'];

    protected $rules=array(
        ////'color_code'=>'required',
        //'color_name'=>'required'
    );

    public function returnDetails(){
        return $this->hasMany('App\Models\Store\ReturnToStoresDetails', 'return_id', 'return_id');
    }

    public function __construct() {
        parent::__construct();
    }
}