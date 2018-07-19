<?php

namespace App\Models\Org;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class OrgDepartments extends BaseValidator
{
  protected $table = 'org_departments';
    protected $primaryKey = 'dep_id';

    const UPDATED_AT = 'updated_date';
    const CREATED_AT = 'created_date';

    protected $fillable = ['dep_id', 'dep_code','dep_name'];
    protected $rules = array(
        
        'dep_code' => 'required',
        'dep_name' => 'required'
    );

    public function __construct() {
        parent::__construct();
        $this->attributes = array(
            'updated_by' => 2//Session::get("user_id")
        );
    }
}
