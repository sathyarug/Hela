<?php
/**
 * Created by PhpStorm.
 * User: sankap
 * Date: 11/4/2018
 * Time: 10:47 PM
 */

namespace App\Models\Store;

use Illuminate\Database\Eloquent\Model;

use App\BaseValidator;



class GrnDetail extends Model
{
    protected $table='store_grn_detail';
    protected $primaryKey='id';
    public $timestamps = false;
    //const UPDATED_AT='updated_date';
    //const CREATED_AT='created_date';

    protected $fillable=['grn_id','grn_number','po_number', 'inv_number', 'sup_code', 'note'];

    protected $rules=array(
        ////'color_code'=>'required',
        //'color_name'=>'required'
    );

    public function __construct() {
        parent::__construct();
    }

}