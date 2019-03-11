<?php

namespace App\Models\Org;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class Designation extends BaseValidator
{
    protected $table = 'org_designation';
    protected $primaryKey = 'des_id';

    const UPDATED_AT = 'updated_date';
    const CREATED_AT = 'created_date';

    protected $fillable = ['des_id', 'des_code','des_name'];
    protected $rules = array(

        'des_code' => 'required',
        'des_name' => 'required'
    );

 public function __construct() {
        parent::__construct();
    }
}
