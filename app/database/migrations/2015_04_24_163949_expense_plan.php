<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ExpensePlan extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cashflow_plan_items', function($table)
		{
			$table->engine = 'InnoDB';
			$table->bigIncrements('id');
			$table->string('description', 255);
			$table->string('category', 255);
			$table->bigInteger('tenant_id')->unsigned();
			$table->integer('amount')->default(0);
			$table->date('start_date');
			$table->date('end_date');
			$table->enum('recurrence', array('Once', 'Weekly', 'Biweekly', 'Monthly'))->default('once');

			$table->foreign('tenant_id')->references('id')->on('tenants');
			$table->index('start_date');
		});

		Schema::create('cashflow_plan_entries', function($table)
		{
			$table->engine = 'InnoDB';
			$table->bigIncrements('id');
			$table->string('description', 255);
			$table->integer('amount');
			$table->bigInteger('item_id')->unsigned();
			$table->enum('entry_type', array('Entry', 'Balance'))->default('Entry');
			$table->date('day');
			$table->foreign('item_id')->references('id')->on('cashflow_plan_items');

		});

		Schema::create('tenant_settings', function($table)
		{
			$table->engine = 'InnoDB';
			$table->bigIncrements('id');
			$table->bigInteger('tenant_id')->unsigned();
			$table->date('payperiod_start');
			$table->enum('payperiod_frequency', array('Weekly', 'Biweekly', 'Monthly'))->default('Biweekly');
			$table->enum('default_billing_frequency', array('Monthly', 'Retainer'))->default('Retainer');
			$table->integer('default_billing_payable_days')->default(0);
			$table->foreign('tenant_id')->references('id')->on('tenants');

		});

		// Add bank info to onboarding table
		Schema::table('companies', function($table)
		{
			$table->enum('billing_frequency', array('Monthly', 'Retainer'))->default('Retainer');
			$table->integer('billing_payable_days')->default(0);
		});

		DB::statement("insert into tenant_settings (id, tenant_id, payperiod_start, payperiod_frequency, default_billing_frequency, default_billing_payable_days) values ('', 1, '2015-02-10', 'Biweekly', 'Retainer', 30)");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement("ALTER TABLE companies DROP COLUMN billing_frequency");
		DB::statement("ALTER TABLE companies DROP COLUMN billing_days");
		DB::statement("DROP TABLE cashflow_plan_entries");
		DB::statement("DROP TABLE cashflow_plan_items");
		DB::statement("DROP TABLE tenant_settings");
	}

}
