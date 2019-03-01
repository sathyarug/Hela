<?php

namespace App\Models\Org;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class SilhouetteClassification extends BaseValidator
{
    protected $table = 'org_silhouette_classification';
    protected $primaryKey = 'sil_class_id';
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = ['sil_class_description'];

    protected $rules = array(
        'sil_class_description' => 'required',

    );

    public function __construct()
    {
        parent::__construct();
        $this->attributes = array(
            'updated_by' => 2//Session::get("user_id")
          );
    }


}
