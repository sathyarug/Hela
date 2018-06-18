<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsrProfileTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('usr_profile', function(Blueprint $table)
		{
			$table->integer('user_id', true);
			$table->integer('loc_id')->index('fk_loc_id_1');
			$table->integer('dept_id')->index('fk_dept_id');
			$table->integer('cost_center_id')->index('fk_cost_center_id');
			$table->string('nic_no', 50)->nullable()->comment('NIC/SOCIAL ID');
			$table->string('first_name');
			$table->string('last_name');
			$table->integer('emp_number')->nullable();
			$table->boolean('status')->nullable();
			$table->date('date_of_birth')->nullable();
			$table->string('gender', 10)->nullable();
			$table->date('joined_date')->nullable();
			$table->integer('reporting_level_1')->nullable()->comment('ureporting level user id');
			$table->integer('reporting_level_2')->nullable();
			$table->string('mobile_no', 25)->nullable();
			$table->string('extension_no', 25)->nullable();
			$table->string('profile_image')->nullable();
			$table->string('civil_status', 10)->nullable();
			$table->date('resign_date')->nullable();
			$table->integer('updated_by')->nullable();
			$table->dateTime('created_date')->nullable();
			$table->integer('created_by')->nullable();
			$table->dateTime('updated_date');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('usr_profile');
	}

}
