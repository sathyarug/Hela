<?php

namespace App\Models\Merchandising;

use App\Http\Controllers\Merchandising\PurchaseOrder;
use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class PoOrderDetails extends BaseValidator
{
    protected $table='merc_po_order_details';
    protected $primaryKey='id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

//    protected $fillable=['po_id'];

    protected $rules=array(
        'po_id'=>'required'
    );



    public function __construct() {
        parent::__construct();
    }

    public function purchaseOrder(){
        return $this->belongsTo(PurchaseOrder::class);
    }
}
