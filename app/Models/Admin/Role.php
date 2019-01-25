<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class Role extends BaseValidator
{
    protected $table='permission_role';
    protected $primaryKey='role_id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

    protected $fillable = ['role_name','role_id'];

    protected $rules = array(
        'role_name' => 'required'
    );

    public function __construct()
    {
        parent::__construct();
    }
}
