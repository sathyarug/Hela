<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCustCustomerTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cust_customer', function(Blueprint $table)
		{
			$table->integer('customer_id', true);
			$table->string('customer_code', 50);
			$table->string('customer_name');
			$table->string('customer_doc_address_1')->nullable();
			$table->string('customer_doc_address_2')->nullable();
			$table->string('customer_city')->nullable();
			$table->string('customer_country')->nullable();
			$table->string('customer_phone', 50)->nullable();
			$table->string('customer_fax', 50)->nullable();
			$table->string('customer_email', 50)->nullable();
			$table->integer('customer_paymnt_method_id')->nullable();
			$table->integer('customer_currency_id')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('cust_customer');
	}

}
