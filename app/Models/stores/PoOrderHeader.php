<?php

namespace App\Models\stores;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class PoOrderHeader extends BaseValidator
{
    protected $table='po_order_header';
    protected $primaryKey='po_id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

//    protected $fillable=[];

    protected $rules=array(
        'po_type'=>'required',
        'po_number'=>'required'
    );

    public function user(){
        return $this->belongsTo('App\Profile', 'user_id');
    }

    public function __construct() {
        parent::__construct();
    }
}
