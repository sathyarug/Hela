<?php
/**
 * Created by PhpStorm.
 * User: sankap
 * Date: 4/22/2019
 * Time: 3:21 PM
 */

namespace App\Models\Store;


class MRNDetail extends Model
{
    protected $table='store_mrn_detail';
    protected $primaryKey='id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';


    protected $fillable=['mrn_id','style_id', 'so_no', 'color', 'size', 'uom', 'item_code', 'mrn_qty', 'bal_qty'];

    protected $rules=array(

    );

    public function __construct() {
        parent::__construct();
    }


}