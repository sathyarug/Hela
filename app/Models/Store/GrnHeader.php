<?php
/**
 * Created by PhpStorm.
 * User: sankap
 * Date: 11/4/2018
 * Time: 10:47 PM
 */

namespace App\Store;

use Illuminate\Database\Eloquent\Model;

use App\BaseValidator;



class GrnHeader extends BaseValidator
{
    protected $table='store_grn_header';
    protected $primaryKey='id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

    protected $fillable=['grn_id','grn_number','po_number', 'inv_number', 'sup_code', 'note', 'created_date', 'created_by'];

    protected $rules=array(
        ////'color_code'=>'required',
        //'color_name'=>'required'
    );

    public function __construct() {
        parent::__construct();
    }

}