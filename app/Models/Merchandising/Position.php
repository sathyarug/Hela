<?php
/**
 * Created by PhpStorm.
 * User: shanilad
 * Date: 9/10/2018
 * Time: 4:55 PM
 */

namespace App\Models\Merchandising;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;



class Position extends BaseValidator {

    protected $table = 'merc_position';
    protected $primaryKey = 'position_id';

    const UPDATED_AT = 'updated_date';
    const CREATED_AT = 'created_date';

    protected $fillable = ['position'];

//    protected $rules = array(
//        'pack_type_description' => 'required'
//
//    );



    public function __construct() {
        parent::__construct();
        $this->attributes = array(
            'updated_by' => 2//Session::get("user_id")
        );
    }

}
