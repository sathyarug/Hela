<?php

namespace App\Models\Finance\Item;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class Category extends BaseValidator
{
    protected $table = 'item_category';
    protected $primaryKey = 'category_id';
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = ['category_code','category_name'];

    protected $rules = array(
        'category_code' => 'required',
        'category_name'  => 'required'/*,
        'category_id'  => 'required'*/
    );

    public function __construct()
    {
        parent::__construct();
        /*$this->attributes = array(
            'updated_by' => 2//Session::get("user_id")
        );*/
    }


}
