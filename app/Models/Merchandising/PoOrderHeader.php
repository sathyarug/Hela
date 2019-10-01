<?php

namespace App\Models\Merchandising;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\BaseValidator;
use App\Libraries\UniqueIdGenerator;

class PoOrderHeader extends BaseValidator
{
    protected $table='merc_po_order_header';
    protected $primaryKey='po_id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

    protected $fillable=['po_type','po_sup_code','po_deli_loc','po_def_cur','po_status','order_type','delivery_date','invoice_to','pay_mode','pay_term','ship_mode','po_date','prl_id','ship_term','special_ins'];

    protected $dates = ['delivery_date','po_date'];
    protected $rules=array(
        //'po_type'=>'required',
        'po_sup_code' => 'required',
        //'po_deli_loc' => 'required',
        'po_def_cur' => 'required',
        'pay_mode' => 'required',
        'pay_term' => 'required',
        //'ship_mode' => 'required',
        'po_date' => 'required',
        'prl_id' => 'required',
        'ship_term' => 'required'
    );


    public function __construct() {
        parent::__construct();
    }

    public function setDiliveryDateAttribute($value)
		{
    	$this->attributes['delivery_date'] = date('Y-m-d', strtotime($value));
    }

    /*public function getDiliveryDateAttribute($value){
    $this->attributes['delivery_date'] = date('d F,Y', strtotime($value));
    return $this->attributes['delivery_date'];
    }*/

    public function setpoDateAttribute($value)
		{
    	$this->attributes['po_date'] = date('Y-m-d', strtotime($value));
    }

    /*public function getpoDateAttribute($value){
    $this->attributes['po_date'] = date('d F,Y', strtotime($value));
    return $this->attributes['delivery_date'];
    }*/

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

        //      if ($model->po_type == 'BULK'){$rep = 'BUL';}
        //  elseif ($model->po_type == 'GENERAL'){$rep = 'GEN';}
        //  elseif ($model->po_type == 'GREAIGE'){$rep = 'GRE';}
        //  elseif ($model->po_type == 'RE-ORDER'){$rep = 'REO';}
        //  elseif ($model->po_type == 'SAMPLE'){$rep = 'SAM';}
        //  elseif ($model->po_type == 'SERVICE'){$rep = 'SER';}
          $user = auth()->payload();
          $user_loc = $user['loc_id'];
          $code = UniqueIdGenerator::generateUniqueId('PO_MANUAL' , $user_loc);
        //  $model->po_number = $rep.$code;
          $model->po_number = $code;
          $model->loc_id = $user_loc;


        });

        /*static::updating(function ($model) {
            $user = auth()->pay_loa();
            $model->updated_by = $user->user_id;
        });*/

        parent::boot();
    }

    public function poDetails(){
        return $this->belongsTo('App\Models\Merchandising\PoOrderDetails' , 'po_id');
    }

    public function getPOSupplierAndInvoice(){
        return self::select('s.supplier_name', 's.supplier_id')
            ->join('org_supplier as s', 's.supplier_id', '=', 'merc_po_order_header.po_sup_code')
            ->get();
    }

    public static function getPoLineData($request){
              $poData=DB::Select("SELECT DISTINCT
style_creation.style_no,
cust_customer.customer_name,
merc_po_order_header.po_id,
merc_po_order_details.id,
item_master.master_description,
org_color.color_name,
org_size.size_name,
org_uom.uom_code,
merc_po_order_details.tot_qty,
merc_customer_order_details.rm_in_date,
merc_customer_order_details.pcd,
merc_customer_order_details.po_no,
merc_customer_order_header.order_id,
item_master.master_id,
item_master.category_id,
(SELECT
                                      IFNULL(SUM(SGD.grn_qty),0)
                                      FROM
                                     store_grn_detail AS SGD

                                     WHERE
                                    SGD.po_details_id = merc_po_order_details.id
                                   ) AS tot_grn_qty,
 (SELECT
                   bal_qty
                      FROM
                      store_grn_detail AS SGD2

                                  WHERE
                                  SGD2.po_details_id = merc_po_order_details.id
																	GROUP BY merc_po_order_details.id
                                ) AS bal_qty,

(

SELECT
IFNULL(sum(for_uom.max),0)as maximum_tolarance
FROM
org_supplier_tolarance AS for_uom
WHERE
for_uom.uom_id =  org_uom.uom_id AND
for_uom.category_id = item_master.category_id AND
for_uom.subcategory_id = item_master.subcategory_id
#GROUP BY(item_master.subcategory_id)


) AS maximum_tolarance


FROM
merc_po_order_header
INNER JOIN merc_po_order_details ON merc_po_order_header.po_number = merc_po_order_details.po_no
INNER JOIN style_creation ON merc_po_order_details.style = style_creation.style_id
INNER JOIN cust_customer ON style_creation.customer_id = cust_customer.customer_id
INNER JOIN merc_customer_order_header ON style_creation.style_id = merc_customer_order_header.order_style
INNER JOIN merc_customer_order_details ON merc_customer_order_header.order_id = merc_customer_order_details.order_id
INNER JOIN item_master ON merc_po_order_details.item_code = item_master.master_id
LEFT JOIN org_supplier_tolarance AS for_category ON item_master.category_id = for_category.category_id
LEFT JOIN org_color ON merc_po_order_details.colour = org_color.color_id
LEFT JOIN org_size ON merc_po_order_details.size = org_size.size_id
LEFT JOIN org_uom ON merc_po_order_details.uom = org_uom.uom_id
/*LEFT JOIN store_grn_detail ON merc_po_order_details.id=store_grn_detail.po_details_id*/

WHERE
merc_po_order_header.po_id = $request->id
AND merc_po_order_header.po_sup_code=$request->sup_id

AND merc_po_order_details.tot_qty>(SELECT
                                      IFNULL(SUM(SGD.grn_qty),0)
                                      FROM
                                     store_grn_detail AS SGD

                                     WHERE
                                    SGD.po_details_id = merc_po_order_details.id
																		#GROUP BY(merc_po_order_details.id)

                                   )
AND merc_po_order_details.po_status='PLANNED'                                   
/*or store_grn_detail.status='0'*/
GROUP BY(merc_po_order_details.id)

");

  //$poData->toArray();
              return $poData;



    }

}
