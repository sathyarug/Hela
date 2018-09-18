<?php

namespace App\Models\Org\Cancellation;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class CancellationReason extends BaseValidator
{
protected $table = 'org_cancellation_reason';
    protected $primaryKey = 'reason_id';

    const UPDATED_AT = 'updated_date';
    const CREATED_AT = 'created_date';

    protected $fillable = ['reason_code', 'reason_description', 'reason_id' , 'reason_category'];
    protected $rules = array(
        'reason_code' => 'required',
        'reason_description' => 'required'
    );

    public function __construct() {
        parent::__construct();
        $this->attributes = array(
            'updated_by' => 2//Session::get("user_id")
        );
    }
}
