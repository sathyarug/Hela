<?php

namespace App\Models\Org;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\BaseValidator;

class Size extends BaseValidator
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'org_size';

    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'updated_date';
    //public $timestamps = false;
    /**
    * The database primary key value.
    *
    * @var string
    */
    protected $primaryKey = 'size_id';


    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['size_id','size_name', 'status'];

    public function scopeLoadCustomeSizeList(){

       return '';//DB::table('cust_sizes')->join('cust_division','cust_division.division_id','=','cust_sizes.division_id' )->join('cust_customer','cust_customer.customer_id','=','cust_sizes.customer_id')->select('cust_sizes.*','cust_division.division_description','cust_customer.customer_name')->where('cust_sizes.status','=','1')->get();

    }



}
