<?php

namespace App\Models\Org\Location;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class Location extends BaseValidator
{
		protected $table = 'org_location';
		protected $primaryKey = 'loc_id';
		const CREATED_AT = 'created_date';
		const UPDATED_AT = 'updated_date';

		protected $fillable = ['loc_code','company_id','loc_name','loc_type','loc_address_1','loc_address_2','city','country_code','loc_phone','loc_fax','time_zone','currency_code','loc_email','loc_web','opr_start_date','postal_code','loc_google','state_Territory','type_of_loc','land_acres','type_property','latitude','longitude'];

    protected $dates = ['opr_start_date'];

  	protected $rules = array(
      'loc_code' => 'required',
      'company_id' => 'required',
      'loc_name' => 'required',
      'loc_type' => 'required',
      'loc_address_1' => 'required',
      /*'loc_address_2' => 'required',*/
      'city' => 'required',
      'country_code' => 'required',
      'loc_phone' => 'required',
      /*'loc_fax' => 'required',*/
      'time_zone' => 'required',
      'currency_code' => 'required',
      'loc_email' => 'required',
      'opr_start_date' => 'required',
      /*'loc_web' => 'required'  */
  	);

    public function setOprStartDateAttribute($value)
		{
    	$this->attributes['opr_start_date'] = date('Y-m-d', strtotime($value));
    }

    public function getOprStartDateAttribute($value){
    $this->attributes['opr_start_date'] = date('d F,Y', strtotime($value));
    return $this->attributes['opr_start_date'];
    }

  	public function __construct()
  	{
      parent::__construct();
  	}

		//location company
		public function company()
		{
			return $this->belongsTo('App\Models\Org\Location\Company' , 'company_id');
		}

		//get location type
		public function locationType()
		{
			return $this->belongsTo('App\Models\Org\LocationType' , 'type_of_loc');
		}

		//property type
		public function propertyType()
		{
			return $this->belongsTo('App\Models\Org\PropertyType');
		}

		//default currency of the company
		public function currency()
		{
			 return $this->belongsTo('App\Currency' , 'currency_code');
		}

		//country of the company
		public function country()
		{
			 return $this->belongsTo('App\Country' , 'country_code');
		}

		//location cost centers
		public function costCenters()
		{
				return $this->belongsToMany('App\Models\Finance\Accounting\CostCenter','org_location_cost_centers','loc_id','cost_center_id')
				->withPivot('id');
		}

}
