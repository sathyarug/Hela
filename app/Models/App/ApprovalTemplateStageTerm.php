<?php

namespace App\Models\App;

use Illuminate\Database\Eloquent\Model;


class ApprovalTemplateStageTerm extends Model
{
    protected $table='app_approval_template_stage_terms';
    protected $primaryKey='id';
    public $timestamps = false;

    protected $rules = array(
        'term_id' => 'required'
    );

    public function __construct()  {
        parent::__construct();
    }

}
