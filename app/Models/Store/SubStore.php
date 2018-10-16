<?php

namespace App\Models\Store;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class SubStore extends BaseValidator
{
    protected $table='org_substore';
    protected $primaryKey='substore_id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

    protected $fillable=['substore_name'];

    protected $rules=array(
        'substore_name'=>'required'
    );

    public function __construct() {
        parent::__construct();
    }
}
