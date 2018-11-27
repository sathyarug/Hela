<?php

namespace App\Models\Merchandising;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class PoOrderHeader extends BaseValidator
{
    protected $table='merc_po_order_header';
    protected $primaryKey='po_id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

//    protected $fillable=[];

    protected $rules=array(
        'po_type'=>'required',
        'po_number'=>'required'
    );


    public function __construct() {
        parent::__construct();
    }

    /*public function poDetails(){
        return $this->hasMany(PoOrderDetails::class);
    }*/

    public function poDetails(){
        return $this->belongsTo('App\Models\Merchandising\PoOrderDetails' , 'po_id');
    }
}
