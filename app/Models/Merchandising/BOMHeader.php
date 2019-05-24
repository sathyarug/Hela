<?php

namespace App\Models\Merchandising;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BOMHeader extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'bom_header';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'bom_id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */

    public $timestamps = false;

    protected $fillable = ['bom_id', 'costing_id'];

    public function getBOMOrderQty($bomID){

        return DB::table('merc_customer_order_details')->select(DB::raw("SUM(order_qty) AS Order_Qty"))
              ->join('bom_so_allocation','bom_so_allocation.order_id','merc_customer_order_details.order_id')
              ->join('bom_header','bom_header.bom_id','bom_so_allocation.bom_id')
              ->where('bom_header.bom_id','=',$bomID)
              ->where('delivery_status','RELEASED')->get();

    }
    
    public function getColorCombpoByCosting($costingId){
        
        return DB::table('costing_bulk_feature_details')
                ->join('org_color','org_color.color_id','=','costing_bulk_feature_details.combo_color')
                ->select('org_color.color_id','org_color.color_name')
                ->where('costing_bulk_feature_details.bulkheader_id',$costingId)
                ->groupBy('org_color.color_id','org_color.color_name')
                ->get();
        
    }
}
