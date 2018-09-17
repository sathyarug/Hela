<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class itemCreation extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'item_master';

    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'master_id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['master_id', 'subcategory_id', 'master_code', 'master_description', 'uom_id', 'status'];

    
}
