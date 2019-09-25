<?php

namespace App\Models\App;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class ProcessApproval extends BaseValidator
{
    protected $table = 'app_process_approvals';
    protected $primaryKey = 'approval_id';
    public $timestamps = false;

    protected $rules = array(
        'process' => 'required'
    );

    public function __construct() {
        parent::__construct();
    }

}
