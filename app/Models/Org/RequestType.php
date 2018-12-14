<?php

namespace App\Models\Org;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class RequestType extends BaseValidator
{
    protected $table = 'org_request_type';
    protected $primaryKey = 'request_type_id';
    //protected $keyType = 'varchar';
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = ['request_type'];

    protected $rules = array(
        'request_type' => 'required'

    );


    public function __construct()
    {
        parent::__construct();
        $this->attributes = array(
            'updated_by' => 2//Session::get("user_id")
        );
    }


}
