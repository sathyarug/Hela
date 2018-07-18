<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TypeOfProperty extends BaseValidator
{
    protected $table = 'type_of_property';
    protected $primaryKey = 'type_prop_id';
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';
    
    protected $fillable = ['type_property'];
    
    protected $rules = array(
        'type_property' => 'required'       
    );
    
    public function __construct()
    {
        parent::__construct();
        $this->attributes = array(
            'updated_by' => 2//Session::get("user_id")
        );
    }
    
    
}
