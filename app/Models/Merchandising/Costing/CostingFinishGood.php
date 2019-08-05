<?php

namespace App\Models\Merchandising\Costing;

use Illuminate\Database\Eloquent\Model;
use App\Libraries\UniqueIdGenerator;
use DB;

use App\BaseValidator;

class CostingFinishGood extends BaseValidator {

    protected $table = 'costing_finish_goods';
    protected $primaryKey = 'fg_id';

    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = ['pack_no', 'combo_color_id', 'feature_id'];

    protected $rules = array(
      /*  'style_id' => 'required',
        'bom_stage_id' => 'required',
        'season_id' => 'required',      */
    );

}
