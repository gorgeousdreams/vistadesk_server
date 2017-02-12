<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePayroll extends Migration {

	/**
	 * Renames "amount" to "total".
	 * Renames "quantity" to two columns: regular_hours and overtime_hours
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('payroll_entries', function($table)
		{
			$table->decimal('regular_hours',8,2)->default(0);
			$table->decimal('overtime_hours',8,2)->default(0);
			$table->bigInteger('total')->default(0);
			$table->bigInteger('employee_comp_amount')->default(0);
			$table->bigInteger('adjustment_amount')->default(0);
			$table->string('adjustment_reason')->nullable()->default(null);
			// Why strings instead of enums? Because this is for historical data only. If the enums in the employees table
			// change, these should not change along with them. Instead they must reflect what value was stored there when
			// payroll was run.
			$table->string('comp_type');
			$table->string('worker_type');
		});
		DB::statement("update payroll_entries p, employees e set p.total = p.amount, p.regular_hours = p.quantity, p.employee_comp_amount = e.comp_amount, p.worker_type = e.worker_type, p.comp_type = e.comp_type where e.id = p.employee_id");
		DB::statement("ALTER TABLE `payroll_entries` DROP COLUMN `quantity`;");
		DB::statement("ALTER TABLE `payroll_entries` DROP COLUMN `amount`;");
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
