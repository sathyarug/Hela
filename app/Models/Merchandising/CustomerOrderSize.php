<?php

namespace App\Models\Merchandising;

use Illuminate\Database\Eloquent\Model;

use App\BaseValidator;
use App\Libraries\UniqueIdGenerator;

class CustomerOrderSize extends BaseValidator
{
    protected $table='merc_customer_order_size';
    protected $primaryKey='id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

    protected $fillable=['details_id','size_id','order_qty','planned_qty'];

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

    //default currency of the company
		public function size()
		{
			 return $this->belongsTo('App\Models\Org\Size' , 'size_id')->select(['size_id','size_name']);
		}



}
