<?php
namespace App\Models\Merchandising;

use Illuminate\Database\Eloquent\Model;
use App\BaseValidator;


class BuyMaster extends BaseValidator{


  protected $table = 'buy_master';
  protected $primaryKey = 'buy_id';

  const UPDATED_AT = 'updated_date';
  const CREATED_AT = 'created_date';

  protected $fillable = ['buy_name'];

<<<<<<< HEAD
  protected $rules=array(
      'buy_name'=>'required'
  );

  public function __construct() {
        parent::__construct();
=======
  //    protected $rules = array(
  //        'pack_type_description' => 'required'
  //
  //    );



  public function __construct() {
      parent::__construct();
      $this->attributes = array(
          'updated_by' => 2//Session::get("user_id")
      );
>>>>>>> 2c35863a5244cc6f3b149820c65dc61e9acb1c35
  }




<<<<<<< HEAD
=======

>>>>>>> 2c35863a5244cc6f3b149820c65dc61e9acb1c35
}
