<?php

namespace App\Models\Merchandising;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;
use App\Libraries\UniqueIdGenerator;

class PoOrderHeader extends BaseValidator
{
    protected $table='merc_po_order_header';
    protected $primaryKey='po_id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

    protected $fillable=['po_type','po_sup_code','po_deli_loc','po_def_cur','po_status','order_type'];

    protected $rules=array(
        'po_type'=>'required',
        'po_sup_code' => 'required',
        'po_deli_loc' => 'required',
        'po_def_cur' => 'required',
        'order_type' => 'required'
    );


    public function __construct() {
        parent::__construct();
    }

    public function currency()
    {
        return $this->belongsTo('App\Models\Finance\Currency' , 'po_def_cur');
    }
    
    public function location()
        {
            return $this->belongsTo('App\Models\Org\Location\Location' , 'po_deli_loc');
        }
        
        
    public function supplier()
        {
            return $this->belongsTo('App\Models\Org\Supplier' , 'po_sup_code');
        }


    public static function boot()
    {
        static::creating(function ($model) {

          if($model->po_type == 'Bulk'){$rep = 'B';}
          elseif ($model->po_type == 'Sample') {$rep = 'S';} 
          elseif ($model->po_type == 'Re-Order') {$rep = 'R';} 
          $user = auth()->user();
          $code = UniqueIdGenerator::generateUniqueId('PO_MANUAL' , $user->location);
          $model->po_number = $rep.$code;
          //$model->updated_by = $user->user_id;
        });

        /*static::updating(function ($model) {
            $user = auth()->user();
            $model->updated_by = $user->user_id;
        });*/

        parent::boot();
    }

    public function poDetails(){
        return $this->belongsTo('App\Models\Merchandising\PoOrderDetails' , 'po_id');
    }
}
