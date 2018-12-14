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

    public static function getPoLineData($request){
        $podata = self::where('d.po_id', $request->id)
            ->join("merc_po_order_details AS d", "merc_po_order_header.po_id", "=", "d.po_id")
            ->join("item_master AS i", "i.master_id", "=", "d.item_code")
            ->select("d.sc_no", "d.bpo", "d.colour", "d.size", "d.uom", "d.bal_qty", "d.id", "i.master_description")
            ->get()
            ->toArray();

        return $podata;
    }
}
