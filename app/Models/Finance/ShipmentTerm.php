<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class ShipmentTerm extends BaseValidator
{
    protected $table = 'fin_shipment_term';
    protected $primaryKey = 'ship_term_id';
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = ['ship_term_code','ship_term_description'];

    protected $rules = array(
        'ship_term_code' => 'required',
        'ship_term_description' => 'required'
    );

  public function __construct()
    {
        parent::__construct();
        /*$this->attributes = array(
            'updated_by' => 2//Session::get("user_id")
        );*/
    }


}
