<?php

namespace App\Models\Org;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class GarmentOptions extends BaseValidator
{
    protected $table='org_garment_options';
    protected $primaryKey='garment_options_id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

    protected $fillable=['garment_options_description','garment_options_id'];

    protected $rules=array(
        'garment_options_description'=>'required'
    );

    public function __construct() {
        parent::__construct();
    }
}
