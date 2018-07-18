<?php

namespace App\Models\Org;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class OrgCompanySection extends BaseValidator
{
  protected $table = 'org_company_section';
    protected $primaryKey = 'sec_id';

    const UPDATED_AT = 'updated_date';
    const CREATED_AT = 'created_date';

    protected $fillable = ['sec_id', 'company_id','section_id'];
    protected $rules = array(
        
        'company_id' => 'required'
    );

    public function __construct() {
        parent::__construct();
        $this->attributes = array(
            'updated_by' => 2//Session::get("user_id")
        );
    }
}
