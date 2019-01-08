<?php


namespace App\Models\Store;

use Illuminate\Database\Eloquent\Model;

use App\BaseValidator;



class Stock extends Model
{
    protected $table='store_stock';
    protected $primaryKey='id';
    //public $timestamps = false;
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

    protected $fillable=['item_code','location', 'customer_po_id','store', 'sub_store', 'uom', 'weighted_average_price', 'inv_qty', 'tolerance_qty', 'total_qty'
    ];

    protected $rules=array(
        ////'color_code'=>'required',
        //'color_name'=>'required'
    );

    public function __construct() {
        parent::__construct();
    }

}