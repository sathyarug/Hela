<?php

namespace App\Models\Merchandising;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class BOMStage extends BaseValidator
{
    protected $table='merc_bom_stage';
    protected $primaryKey='bom_stage_id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

    protected $fillable=['bom_stage_description','bom_stage_id'];

    protected $rules=array(
        'bom_stage_description'=>'required'
    );

    public function __construct() {
        parent::__construct();
    }
}
