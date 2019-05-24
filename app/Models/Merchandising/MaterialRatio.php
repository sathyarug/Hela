<?php

namespace App\Models\Merchandising;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class MaterialRatio extends \App\BaseValidator
{
    protected $table='mat_ratio';
    protected $primaryKey='bom_id';
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';
    
    protected $fillable=['bom_id','component_id','master_id','color_id','size_id', 'required_qty'];
    
    protected $rules=array(
        'bom_id'=>'required',
        'component_id'=>'required',
        'master_id'=>'required',
        'color_id'=>'required',
        'size_id'=>'required'
    );
    
    public function __construct() {
        parent::__construct();
    }
    
}
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

