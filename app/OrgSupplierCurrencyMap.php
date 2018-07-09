<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrgSupplierCurrencyMap extends Model
{
    protected $table='org_supplier_currency_map';
    protected $primaryKey='supplier_currency_map_id';
    const UPDATED_AT='updated_date';
    const CREATED_AT='created_date';

    protected $fillable = ['supplier_id','currency_id'];

}
