<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsrLoginTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('usr_login', function(Blueprint $table)
		{
			$table->integer('user_id')->index('user_id');
			$table->string('user_name')->nullable();
			$table->string('password')->nullable();
			$table->dateTime('password_reset_date')->nullable()->comment('PASSWORD LAST UPDATED DATE ');
			$table->dateTime('last_logged_date')->nullable();
			$table->string('remember_token')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('usr_login');
	}

}
