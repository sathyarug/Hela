<?php

namespace App\Models\Merchandising;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class bom_details extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'bom_details';

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

    protected $fillable = ['bom_id','combine_id','item_code','item_color','item_size','unit_price','conpc','total_qty','total_value','supplier_id','artical_no','status','bal_qty','order_id','uom_id'];

    public function GetBOMDetails($bomId){

        return DB::table('bom_details')
                  ->join('item_master','item_master.master_id','bom_details.item_code')
                  ->join('org_color','org_color.color_id','bom_details.item_color')
                  ->join('merc_mat_size','merc_mat_size.mat_size_id','bom_details.item_size')
                  ->join('org_uom','org_uom.uom_id','bom_details.uom_id')
                  ->select('item_master.master_id','item_master.master_description','bom_details.artical_no','org_color.color_name','merc_mat_size.dimensions','org_uom.uom_description','org_uom.uom_id','bom_details.conpc','bom_details.item_wastage','bom_details.unit_price','bom_details.total_qty','bom_details.total_value','org_color.color_id')
                  ->where('bom_details.bom_id',$bomId)->get();
    }
}
