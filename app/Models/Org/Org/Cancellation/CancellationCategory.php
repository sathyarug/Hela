<?php

namespace App\Models\Org\Cancellation;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;


class CancellationCategory extends BaseValidator
{
   protected $table = 'org_cancellation_category';
    protected $primaryKey = 'category_id';

    const UPDATED_AT = 'updated_date';
    const CREATED_AT = 'created_date';

    protected $fillable = ['category_code', 'category_description', 'category_id'];
    protected $rules = array(
        'category_code' => 'required',
        'category_description' => 'required'
    );

   public function __construct() {
        parent::__construct();
        $this->attributes = array(
            'updated_by' => 2//Session::get("user_id")
        );
    }
}
