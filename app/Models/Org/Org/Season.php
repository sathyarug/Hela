<?php

namespace App\Models\Org;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class Season extends BaseValidator {

    protected $table = 'org_season';
    protected $primaryKey = 'season_id';

    const UPDATED_AT = 'updated_date';
    const CREATED_AT = 'created_date';

    protected $fillable = ['season_code', 'season_name', 'season_id'];
    protected $rules = array(
        'season_code' => 'required',
        'season_name' => 'required'
    );

    public function __construct() {
        parent::__construct();
        $this->attributes = array(
            'updated_by' => 2//Session::get("user_id")
        );
    }

}
