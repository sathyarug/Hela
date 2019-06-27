<?php

namespace App\Models\Org;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\BaseValidator;

class OriginType extends BaseValidator
{
    protected $table = 'org_origin_type';
    protected $primaryKey = 'origin_type_id';
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = ['origin_type','origin_type_id'];

    protected $rules = array(
        'origin_type' => 'required'
    );

   public function __construct()
    {
        parent::__construct();
    }

    public function isUsed($id){
      $is_exists = DB::table('item_master')->where('uom_id', $id)->exists();
    }

}
