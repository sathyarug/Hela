
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BaseValidator extends Model
{
    protected $rules = array();
    protected $errors;
    protected $errors_str;

    public function validate($data)
    {
        // make a new validator object
        $v = \Illuminate\Support\Facades\Validator::make($data, $this->rules);

        // check for failure
        if ($v->fails()) {
            // set errors and return false
            $this->errors = $v->errors();
            $this->errors_str = implode(",",$v->messages()->all());
            return false;
        }
        // validation pass
        return true;
    }

    public function errors()
    {
        return $this->errors;
    }
    
    public function errors_tostring(){
        return $this->errors_str;
    }
}



