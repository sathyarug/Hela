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



class ColorOption extends BaseValidator {

    protected $table = 'merc_color_options';
    protected $primaryKey = 'col_opt_id';

    const UPDATED_AT = 'updated_date';
    const CREATED_AT = 'created_date';

    protected $fillable = ['color_option'];

    protected $rules = array(
        'color_option' => 'required'

    );



    public function __construct() {
        parent::__construct();
        $this->attributes = array(
            'updated_by' => 2//Session::get("user_id")
        );
    }

}
