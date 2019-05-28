<?php

namespace App\Models\Merchandising;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;
use App\Libraries\UniqueIdGenerator;
use DB;

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
    
//    public static function boot()
//    {
//        static::creating(function ($model) {
//          $payload = auth()->payload();
//
//          $code = UniqueIdGenerator::generateUniqueId('BULK_COSTING' , $payload->get('loc_id') );
//          $model->seq_id = $code;
//        });
//
//
//        parent::boot();
//    }

    public static function getCostingAndStyleData($id){

         $result = self::join('style_creation', 'style_creation.style_id',   '=', 'costing_bulk.style_id')
             ->join('merc_bom_stage', 'costing_bulk.bom_stage_id', '=', 'merc_bom_stage.bom_stage_id')
             ->leftjoin('merc_costing_so_combine', 'costing_bulk.bulk_costing_id', '=', 'merc_costing_so_combine.costing_id')
             ->where('costing_bulk.style_id', '=',$id)
             ->select('style_creation.style_no', 'costing_bulk.bulk_costing_id',  'merc_bom_stage.bom_stage_description', DB::raw('group_concat(merc_costing_so_combine.details_id) so_no'))
             ->groupBy('style_creation.style_no', 'costing_bulk.bulk_costing_id', 'merc_bom_stage.bom_stage_description')
             ->get();
             //->toSql();

        return $result;

    }

    public static function getSoListByStyle($style){
            return CustomerOrder::select('merc_customer_order_header.order_code','org_color.color_name','org_color.color_id', 'org_country.country_description', 'merc_customer_order_details.order_qty', 'merc_customer_order_details.details_id')
                ->join('merc_customer_order_details', 'merc_customer_order_details.order_id', '=', 'merc_customer_order_header.order_id')
                ->join('org_color', 'org_color.color_id', '=', 'merc_customer_order_details.style_color')
                ->join('org_country', 'org_country.country_id', '=', 'merc_customer_order_details.country')
                ->where([['order_style', '=', $style]])
                ->groupBy('org_color.color_name', 'org_color.color_id', 'merc_customer_order_header.order_code', 'org_country.country_description', 'merc_customer_order_details.order_qty', 'merc_customer_order_details.details_id')
                ->get();
    }

    public static function getCostingCombineData($styleId){
        $rr = DB::select('SELECT
                                    `costing_bulk_feature_details`.`bulkheader_id`,`merc_costing_so_combine`.`feature_id`, `style_creation`.`style_no`,
                                    `style_creation`.`style_id`,
                                    `merc_bom_stage`.`bom_stage_description`,
                                    GROUP_CONCAT(
                                        DISTINCT merc_customer_order_header.order_code
                                    ) AS so,
                                    `org_color`.`color_name`,
                                    `org_color`.`color_id`
                                FROM
                                    `costing_bulk`
                                INNER JOIN `costing_bulk_feature_details` ON `costing_bulk_feature_details`.`bulkheader_id` = `costing_bulk`.`bulk_costing_id`
                                INNER JOIN `style_creation` ON `style_creation`.`style_id` = `costing_bulk`.`style_id`
                                LEFT JOIN `merc_costing_so_combine` ON `merc_costing_so_combine`.`costing_id` = `costing_bulk`.`bulk_costing_id`and merc_costing_so_combine.color = `costing_bulk_feature_details`.`combo_color`
                                LEFT JOIN `merc_customer_order_details` ON `merc_customer_order_details`.`details_id` = `merc_costing_so_combine`.`details_id`
                                LEFT JOIN `merc_customer_order_header` ON `merc_customer_order_header`.`order_id` = `merc_customer_order_details`.`order_id`
                                INNER JOIN `merc_bom_stage` ON `merc_bom_stage`.`bom_stage_id` = `costing_bulk_feature_details`.`bom_stage`
                                INNER JOIN `org_color` ON `costing_bulk_feature_details`.`combo_color` = `org_color`.`color_id`
                                WHERE
                                    (
                                        `costing_bulk`.`status` = 1
                                        AND `costing_bulk`.`style_id` = '.$styleId.'
                                    )
                                GROUP BY
                                    `costing_bulk_feature_details`.`combo_color`,
                                    `costing_bulk_feature_details`.`bulkheader_id`,
                                    `merc_costing_so_combine`.`color`
                                ORDER BY
                                    `costing_bulk`.`bulk_costing_id` ASC');

        dd($rr);
    }


}
