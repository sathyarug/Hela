<?php

namespace App\Models\Org;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class Color extends BaseValidator
{
    protected $table='org_color';
    protected $primaryKey='color_id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

    protected $fillable=['color_code','color_name','color_id'];

    protected $rules=array(
        'color_code'=>'required',
        'color_name'=>'required'
    );

    public function __construct() {
        parent::__construct();
    }
}
