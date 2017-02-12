<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeActivityTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::dropIfExists('employee_activities');
		Schema::create('employee_activities', function($table)
		{
			$table->engine = 'InnoDB';
			$table->bigIncrements('id');
			$table->bigInteger('employee_id');
			$table->bigInteger('action_user_id')->nullable()->default(null);
			$table->string('content');
			$table->string('comment')->nullable()->default(null);
			$table->timestamps();
		});

		Schema::table('pay_time_off', function($table)
		{
			$table->bigInteger('manager_id')->nullable()->default(null)->after('status');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('employee_activities');

		if (Schema::hasColumn('pay_time_off', 'manager_id')) {
			Schema::table('pay_time_off', function($table)
			{
				$table->dropColumn('manager_id');
			});
		}
	}

}
