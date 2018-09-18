<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class GoodsType extends BaseValidator
{
    protected $table = 'fin_goods_type';
    protected $primaryKey = 'goods_type_id';
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = ['goods_type_description'];

    protected $rules = array(
        'goods_type_description' => 'required'
    );

    public function __construct()
    {
        parent::__construct();  
    }


}
