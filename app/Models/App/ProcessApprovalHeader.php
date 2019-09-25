<?php

namespace App\Models\App;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;

class ProcessApprovalHeader extends BaseValidator
{
    protected $table = 'app_process_approval_header';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $rules = array(
        'process' => 'required'
    );

    public function __construct() {
        parent::__construct();
    }

}
