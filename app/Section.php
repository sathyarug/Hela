<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Section extends BaseValidator
{
  protected $table = 'org_section';
    protected $primaryKey = 'section_id';

    const UPDATED_AT = 'updated_date';
    const CREATED_AT = 'created_date';

    protected $fillable = ['section_code', 'section_name', 'section_id'];
    protected $rules = array(
        'section_code' => 'required',
        'section_name' => 'required'
    );

    public function __construct() {
        parent::__construct();
        $this->attributes = array(
            'updated_by' => 2//Session::get("user_id")
        );
    }
}
