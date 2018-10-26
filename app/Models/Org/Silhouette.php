<?php

namespace App\Models\Org;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class Silhouette extends BaseValidator
{
    protected $table='product_Silhouette';
    protected $primaryKey='product_silhouette_id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

    protected $fillable=['product_silhouette_description','product_silhouette_id'];

    protected $rules=array(
        'product_silhouette_description'=>'required'
    );

    public function __construct() {
        parent::__construct();
    }
}
