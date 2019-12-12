<?php

namespace App\Models\Merchandising\Costing;

use Illuminate\Database\Eloquent\Model;
use App\Libraries\UniqueIdGenerator;
use DB;

use App\BaseValidator;

class CostingSfgColor extends BaseValidator {

    protected $table = 'costing_sfg_color';
    protected $primaryKey = 'sfg_color_id';

    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = ['sfg_color_id', 'fng_color_id', 'color_id'];


    //Validation functions......................................................
    /**
    *unique:table,column,except,idColumn
    *The field under validation must not exist within the given database table
    */
    protected function getValidationRules($data /*model data with attributes*/) {
      return [
        'fng_color_id' => 'required',
        'color_id' => 'required'
      ];
    }


}
