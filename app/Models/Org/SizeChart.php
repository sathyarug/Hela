<?php

namespace App\Models\Org;
use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class SizeChart extends BaseValidator
{

    protected $table = 'org_size_chart';
    protected $primaryKey = 'size_chart_id';
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = ['size_chart_id','chart_name'];
    
    protected $rules=array(
        'chart_name'=>'required',
        'description'=>'required'
    );

    public function __construct()
    {
        parent::__construct();
    }


}
