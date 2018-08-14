<?php

namespace App\Models\Org\Location;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class Company extends BaseValidator
{

		protected $table = 'org_company';
		protected $primaryKey = 'company_id';
		const CREATED_AT = 'created_date';
		const UPDATED_AT = 'updated_date';

		protected $fillable = ['company_code','group_id','company_name','company_address_1','company_address_2','city','country_code','company_fax','company_contact_1','company_contact_2','company_logo','company_email','company_web','default_currency','finance_month','company_remarks','vat_reg_no','tax_code','company_reg_no'];

    	protected $rules = array(
        'company_code' => 'required',
        'company_name' => 'required',
        'group_id' => 'required',
        'company_address_1' => 'required',
        'company_address_2' => 'required',
        'city' => 'required',
        'country_code' => 'required',
        'company_reg_no' => 'required',
        'company_contact_1' => 'required',
        'company_email' => 'required',
        'default_currency' => 'required',
        'finance_month' => 'required',
        'vat_reg_no'  => 'required',
        'tax_code'  => 'required'
    	);

    	public function __construct()
    	{
        parent::__construct();
        $this->attributes = array(
            'updated_by' => 2//Session::get("user_id")
        );
    	}

			//departments belongs to the company
			public function departments()
	    {
	        return $this->belongsToMany('App\Models\Org\OrgDepartments','org_company_departments','company_id','dep_id')
					->withPivot('id');
	    }

			//sections belongs to the company
			public function sections()
	    {
	        return $this->belongsToMany('App\Section','org_company_sections','company_id','section_id')
					->withPivot('id');
	    }

			//default currency of the company
			public function currency()
			{
				 return $this->belongsTo('App\Currency' , 'default_currency');
			}

			//country of the company
			public function country()
			{
				 return $this->belongsTo('App\Country' , 'country_code');
			}

}
