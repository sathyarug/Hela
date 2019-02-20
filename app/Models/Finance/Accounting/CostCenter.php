<?php

namespace App\Models\Finance\Accounting;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class CostCenter extends BaseValidator
{
    protected $table = 'org_cost_center';
    protected $primaryKey = 'cost_center_id';
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = ['cost_center_code','loc_id','cost_center_name'];

    protected $rules = array(
        'cost_center_code' => 'required',
        'cost_center_name'  => 'required'
    );

    public function __construct()
    {
        parent::__construct();
        /*$this->attributes = array(
            'updated_by' => 2//Session::get("user_id")
        );*/
    }


}
