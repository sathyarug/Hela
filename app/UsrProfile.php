<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UsrProfile extends Model
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
        'joined_date'
    ];



    //protected $except = ['_token'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    /*protected $hidden = [
        'password', 'remember_token',
    ];*/
}
