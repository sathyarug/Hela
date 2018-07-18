<?php

namespace App\Models\Org;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class OrgCompanyDepartments extends BaseValidator
{
  protected $table = 'org_company_departments';
    protected $primaryKey = 'com_dep_id';

    const UPDATED_AT = 'updated_date';
    const CREATED_AT = 'created_date';

    protected $fillable = ['com_dep_id', 'company_id','com_dep_name'];
    protected $rules = array(
        
        'company_id' => 'required',
        'com_dep_name' => 'dep_name'
    );

    public function __construct() {
        parent::__construct();
        $this->attributes = array(
            'updated_by' => 2//Session::get("user_id")
        );
    }
}
