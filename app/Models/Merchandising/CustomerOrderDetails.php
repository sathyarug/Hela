<?php

namespace App\Models\Merchandising;

use Illuminate\Database\Eloquent\Model;

use App\BaseValidator;
use App\Libraries\UniqueIdGenerator;

class CustomerOrderDetails extends BaseValidator
{
    protected $table='merc_customer_order_details';
    protected $primaryKey='details_id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

    protected $fillable=['order_id','style_color','style_description','pcd','rm_in_date','po_no','planned_delivery_date','revised_delivery_date',
    'fob','country','projection_location','order_qty','excess_presentage','planned_qty','ship_mode','delivery_status'];

    protected $rules=array(
      /*  'order_id'=>'required',
        'style_color'=>'required',
        'style_description' => 'required',
        'pcd' => 'required',
        'rm_in_date' => 'required',
        'po_no' => 'required',
        'planned_delivery_date' => 'required',
        'revised_delivery_date' => 'required',
        'fob' => 'required',
        'country' => 'required',
        'projection_location' => 'required',
        'order_qty' => 'required',
        'excess_presentage' => 'required'.
        'planned_qty' => 'required',
        'ship_mode' => 'required',
        'delivery_status' => 'required'*/
    );

    public function __construct() {
        parent::__construct();
    }

}
