<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BaseValidator extends Model
{
    protected $rules = array();
    protected $errors;
    protected $errors_str;


    public static function boot()
    {
        static::creating(function ($model) {
          $user = auth()->user();
          $model->created_by = $user->user_id;
          $model->updated_by = $user->user_id;
        });

        static::updating(function ($model) {
            $user = auth()->user();
            $model->updated_by = $user->user_id;
        });

        /*static::deleting(function ($model) {
            // bluh bluh
        });*/

        parent::boot();
    }



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
