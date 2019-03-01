<?php

namespace App\Models\Merchandising;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;
use App\Libraries\UniqueIdGenerator;

class BulkCostingFeatureDetails extends BaseValidator {

    protected $table = 'costing_bulk_feature_details';
    protected $primaryKey = 'blk_feature_id';

    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';


    protected $fillable = ['mcq'];

}
