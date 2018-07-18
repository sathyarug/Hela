<?php

namespace App\Models\Org\Location;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class LocationCostCenter extends BaseValidator
{
  protected $table = 'org_location_cost_center';
    protected $primaryKey = 'cost_id';

    const UPDATED_AT = 'updated_date';
    const CREATED_AT = 'created_date';

    protected $fillable = ['loc_id', 'cost_name'];
    protected $rules = array(
        'loc_id' => 'required',
        'cost_name' => 'required'
    );

    public function __construct() {
        parent::__construct();
        $this->attributes = array(
            'updated_by' => 2//Session::get("user_id")
        );
    }
}
