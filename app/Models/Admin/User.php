<?php

namespace App\Models\Admin;

use App\BaseValidator;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;


class User extends BaseValidator
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'usr_login';
    protected $primaryKey = 'user_id';
    protected $fillable = [
        'user_name', 'email', 'password',
    ];

    public $timestamps = false;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password'
    ];

    protected $rules = array(
        'user_name' => 'required',
        'password' => 'required'
    );
}
