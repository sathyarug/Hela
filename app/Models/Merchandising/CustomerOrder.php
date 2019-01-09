<?php

namespace App\Models\Merchandising;

use Illuminate\Database\Eloquent\Model;

use App\BaseValidator;
use App\Libraries\UniqueIdGenerator;

class CustomerOrder extends BaseValidator
{
    protected $table='merc_customer_order_header';
    protected $primaryKey='order_id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

    protected $fillable=['order_style','order_customer','order_division','order_type','order_status'];

    protected $rules=array(
        'order_style'=>'required',
        'order_customer'=>'required',
        'order_division' => 'required',
        'order_type' => 'required',
        /*'order_status' => 'required'*/
    );

    public function __construct() {
        parent::__construct();
    }


    public static function boot()
    {
        static::creating(function ($model) {
          $user = auth()->user();
          $code = UniqueIdGenerator::generateUniqueId('CUSTOMER_ORDER' , $user->location);
          $model->order_code = $code;
          //$model->updated_by = $user->user_id;
        });

        /*static::updating(function ($model) {
            $user = auth()->user();
            $model->updated_by = $user->user_id;
        });*/

        parent::boot();
    }


		public function style()
		{
			 return $this->belongsTo('App\Models\Merchandising\styleCreation' , 'order_style')->select(['style_id','style_no','style_description']);
		}

    public function customer()
		{
			 return $this->belongsTo('App\Models\Org\Customer' , 'order_customer')
          ->select(['customer_id','customer_code','customer_name'])->with(['divisions']);
		}

    /*public function order_type()
		{
			 return $this->belongsTo('App\Models\Merchandising\CustomerOrderType' , 'order_type')->select(['order_type_id','order_type']);
		}*/

}
