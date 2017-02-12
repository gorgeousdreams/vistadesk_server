<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixDbAndBankInfo extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::hasColumn('employees', 'documents')) {
			DB::statement("ALTER TABLE `employees` DROP COLUMN `documents`;");
		}
		if (Schema::hasColumn('employees', 'signature')) {
			DB::statement("ALTER TABLE `employees` DROP COLUMN `signature`;");
		}

		// Add bank info to onboarding table
		Schema::table('onboarding', function($table)
		{
			$table->boolean('bank_info')->default(false);
			$table->boolean('w4')->default(false);
			$table->boolean('i9')->default(false);
			$table->boolean('other_docs')->default(false);
		});

		Schema::create('secure_info', function($table)
		{
			$table->engine = 'InnoDB';
			$table->bigIncrements('id');
			$table->bigInteger('employee_id')->unsigned();
			$table->string('bank_account', 1024)->nullable();
			$table->string('bank_routing', 1024)->nullable();
			$table->string('bank_account_type', 1024)->nullable();
			$table->string('ssn', 1024)->nullable();

			$table->foreign('employee_id')->references('id')->on('employees');
//			$table->foreign('employee_id')->references('id')->on('employees');
//			$table->index('employee_id');

		});

		DB::statement("ALTER TABLE employees CHANGE COLUMN status status ENUM('Active', 'Inactive', 'Onboarding', 'Terminated', 'Quit')");

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
	}

}
