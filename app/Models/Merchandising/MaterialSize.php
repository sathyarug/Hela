<?php

namespace App\Models\Merchandising;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class MaterialSize extends BaseValidator
{
    protected $table='merc_mat_size';
    protected $primaryKey='mat_size_id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

    protected $fillable=['category_id','subcategory_id','mat_size_id','dimensions'];

    protected $rules=array(
        'category_id'=>'required',
        'subcategory_id'=>'required',
        'dimensions'=>'required'
    );

    public function __construct() {
        parent::__construct();
    }
}
