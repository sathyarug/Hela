<?php

namespace App\Models\Merchandising;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;
use App\Libraries\UniqueIdGenerator;

class BulkCostingDetails extends BaseValidator {

    protected $table = 'costing_bulk_details';
    protected $primaryKey = 'item_id';

    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

//    protected $fillable = ['item_id','bulk_costing_id'];

   

}
