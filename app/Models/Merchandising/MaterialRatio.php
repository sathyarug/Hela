<?php

namespace App\Models\Merchandising;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use App\BaseValidator;

class MaterialRatio extends \App\BaseValidator
{
    protected $table='mat_ratio';
    protected $primaryKey='bom_id';
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';
    
    protected $fillable=['bom_id','component_id','master_id','color_id','size_id', 'required_qty','order_id','status'];
    
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
    
    public function getMaterialRatio($bomid, $componentid, $masteritemid){
        
        return DB::table('mat_ratio')
                ->join('org_color','mat_ratio.color_id','org_color.color_id')
                ->join('org_size','mat_ratio.size_id','org_size.size_id')
                ->select('org_color.color_name','org_size.size_name','mat_ratio.required_qty','order_id')
                ->where('mat_ratio.bom_id',$bomid)
                ->where('mat_ratio.master_id',$masteritemid)
                ->where('mat_ratio.component_id',$componentid)
                ->where('mat_ratio.status','1')
                ->get();
        
        
    }
    
}
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

