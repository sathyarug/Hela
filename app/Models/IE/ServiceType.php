<?php

namespace App\Models\IE;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class ServiceType extends BaseValidator
{
 protected $table = 'ie_service_type';
    protected $primaryKey = 'service_type_id';

    const UPDATED_AT = 'updated_date';
    const CREATED_AT = 'created_date';

    protected $fillable = ['service_type_code', 'service_type_description', 'service_type_id'];
    protected $rules = array(
        'service_type_code' => 'required',
        'service_type_description' => 'required'
    );

    public function __construct() {
        parent::__construct();
        $this->attributes = array(
            'updated_by' => 2//Session::get("user_id")
        );
    }
}
