<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Division extends BaseValidator
{
    protected $table='cust_division';
    protected $primaryKey='division_id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';
    
    protected $fillable=['division_code','division_description','division_id'];
    
    protected $rules=array(
        'division_code'=>'required',
        'division_description'=>'required'
    );
    
    public function __construct() {
        parent::__construct();
        $this->attributes=array(
            'updated_by' => 2//Session::get("user_id")
            );
    }
}
