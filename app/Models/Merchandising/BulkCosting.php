<?php

namespace App\Models\Merchandising;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;
use App\Libraries\UniqueIdGenerator;

class BulkCosting extends BaseValidator {

    protected $table = 'costing_bulk';
    protected $primaryKey = 'bulk_costing_id';

    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = [
        'bulk_costing_id', 'seq_id','cust_id', 'division_id',
        'season_id', 'style_id', 'delivery_id', 'color_id', 
        'costed_smv_id','style_remark','color_type_id',
        'total_order_qty','pcd','total_cost','fob','plan_efficiency',
        'cost_per_min','cost_per_std_min','epm','np_margin','obsolete_date',
        'user_loc_id',
        'status'];
    protected $rules = array(
        'bulk_costing_id' => '',
        //'cust_id' => 'required',
       // 'division_id' => 'required'
    );
    
    public static function boot()
    {
        static::creating(function ($model) {
          $payload = auth()->payload();
          
          $code = UniqueIdGenerator::generateUniqueId('BULK_COSTING' , $payload->get('loc_id') );
          $model->seq_id = $code;
        });


        parent::boot();
    }

   

}
