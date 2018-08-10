<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class assign_property extends Model
{
    protected $table = 'item_property_assign';
    
    protected $primaryKey = 'property_assign_id';
    
    protected $fillable = ['property_assign_id','property_id','subcategory_id','status', 'sequence_no'];
    
   
    
}
