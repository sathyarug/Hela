<?php

namespace App\Models\Org\Location;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class Source extends BaseValidator
{

		protected $table = 'org_source';
		protected $primaryKey = 'source_id';
		const CREATED_AT = 'created_date';
		const UPDATED_AT = 'updated_date';

		protected $fillable = ['source_name','source_code','source_id'];

    	protected $rules = array(
        'source_code' => 'required',
        'source_name'  => 'required'
    	);

    /*	public function __construct()
    	{
        parent::__construct();
    	}*/
}
