<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable
{
    use Notifiable;
    use HasRoles;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'usr_profile';
    protected $primaryKey = 'user_id';
    protected $fillable = [
        '_token',  'loc_id', 'dept_id', 'cost_center_id', 'desig_id', 'nic_no', 'first_name', 'last_name', 'date_of_birth', 'gender', 'civil_status',
        'joined_date', 'mobile_no', 'email', 'emp_number', 'loc_id', 'dept_id', 'desig_id', 'cost_center_id', 'resign_date', 'reporting_level_1',
        'reporting_level_2'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'joined_date'
    ];

    private function setJoinedDateValue() {
        return date('Y-m-d', strtotime($this->attributes['joined_date']));
    }

    /*private function getJoinedDateValue() {
        return $this->attributes['dob']->format('m/d/Y');
        return date('Y-m-d', strtotime($this->attributes['joined_date']));
    }*/

    private function setDateOfBirthValue($value) {
        //echo 'test'; exit;
        $this->attributes['date_of_birth'] = date('Y-m-d', strtotime($this->attributes['date_of_birth']));
    }

    private function getDateOfBirthAttribute() {
           return $this->attributes['date_of_birth']->format('Y-m-d');
           return date('Y-m-d', strtotime($this->attributes['date_of_birth']));
       }



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
