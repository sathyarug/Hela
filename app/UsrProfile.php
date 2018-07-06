<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UsrProfile extends BaseValidator
{
    protected $table = 'usr_profile';
    protected $primaryKey = 'user_id';
    protected $fillable = [
        'loc_id', 'dept_id', 'cost_center_id', 'desig_id', 'nic_no', 'first_name', 'last_name', 'date_of_birth', 'gender', 'civil_status',
        'joined_date', 'mobile_no', 'email', 'emp_number', 'loc_id', 'dept_id', 'desig_id', 'cost_center_id', 'resign_date', 'reporting_level_1',
        'reporting_level_2'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'joined_date',
        'resign_date',
        'date_of_birth'
    ];

    protected $rules = array(
        'loc_id' => 'required',
        'dept_id' => 'required',
        'cost_center_id' => 'required',
        'desig_id' => 'required',
        'first_name' => 'required',
        'last_name' => 'required',
        'emp_number' => 'required',
        'nic_no' => 'required',
        'email' => 'required',
        'gender' => 'required',

    );

    public function setDateOfBirthAttribute($value){
        $this->attributes['date_of_birth'] = date('Y-m-d', strtotime($value));
    }

    public function setJoinedDateAttribute($value){
        $this->attributes['joined_date'] = date('Y-m-d', strtotime($value));
    }

    public function setResignDateAttribute($value){
        $this->attributes['resign_date'] = date('Y-m-d', strtotime($value));
    }


}
