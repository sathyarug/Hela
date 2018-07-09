
<?php

namespace App\Models\Org\Location;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class Main_Cluster extends BaseValidator
{
	
		protected $table = 'org_group';
		protected $primaryKey = 'group_id';
		const CREATED_AT = 'created_date';
		const UPDATED_AT = 'updated_date';
   
		protected $fillable = ['source_id','group_code','group_name'];
    
    	protected $rules = array(
        'source_id' => 'required',
        'group_code' => 'required',
        'group_name'  => 'required'        
    	);
    
    	public function __construct()
    	{
        parent::__construct();
        $this->attributes = array(
            'updated_by' => 2//Session::get("user_id")
        );
    	}
}

