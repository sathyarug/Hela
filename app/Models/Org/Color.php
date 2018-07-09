<?php

namespace App\Models\Org;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class Color extends BaseValidator
{
    protected $table = 'org_color';
    protected $primaryKey = 'color_id';
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = ['color_id','color_code','color_name'];

    protected $rules = array(
        'color_code' => 'required',
        'color_name' => 'required'
    );

    public function __construct()
    {
        parent::__construct();
        $this->attributes = array(
            'updated_by' => 2//Session::get("user_id")
        );
    }


}
