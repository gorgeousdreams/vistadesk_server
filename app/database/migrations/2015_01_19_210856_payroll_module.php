<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PayrollModule extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('payroll_periods', function($table)
		{
			$table->engine = 'InnoDB';
			$table->bigIncrements('id');
			$table->date('start_date');
			$table->date('end_date');
			$table->dateTime('processed_at')->nullable();
			$table->dateTime('created_at');
			$table->dateTime('updated_at')->nullable();
			$table->string('uuid', 128)->nullable();

			$table->bigInteger('tenant_id')->unsigned();
			$table->foreign('tenant_id')->references('id')->on('tenants');

			$table->index('tenant_id');
			$table->index('start_date');
			$table->index('uuid');

		});

		Schema::create('payroll_entries', function($table)
		{
			$table->engine = 'InnoDB';
			$table->bigIncrements('id');
			$table->bigInteger('employee_id')->unsigned();
			$table->bigInteger('period_id')->nullable()->unsigned();
			$table->bigInteger('amount');
			$table->bigInteger('quantity');
			$table->dateTime('created_at');
			$table->dateTime('updated_at')->nullable();
			$table->string('memo', 1024);
			$table->string('uuid', 128)->nullable();

			$table->foreign('employee_id')->references('id')->on('employees');
			$table->foreign('period_id')->references('id')->on('payroll_periods');

			// apparently, these are created automatically by the InnoDB engine... 
			// http://dev.mysql.com/doc/refman/5.0/en/innodb-foreign-key-constraints.html
			$table->index('employee_id');
			$table->index('period_id');
			$table->index('uuid');
		});

		DB::update(DB::raw("insert into settings (name, value, tenant_id) values ('payroll.graceDays', '2', 1), ('payroll.firstPeriodStart', '2014-12-28', 1), ('payroll.runsAt', '10:00', 1), ('payroll.frequency', 'Bi-Weekly', 1)"));

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
