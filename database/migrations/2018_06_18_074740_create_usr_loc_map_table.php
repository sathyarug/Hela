<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsrLocMapTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('usr_loc_map', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('user_id')->nullable();
			$table->string('loc_code', 10)->nullable();
			$table->integer('created_by')->nullable();
			$table->dateTime('created_date')->nullable();
			$table->integer('updated_by')->nullable();
			$table->dateTime('updated_date')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('usr_loc_map');
	}

}
