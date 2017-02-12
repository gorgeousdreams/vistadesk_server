<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NonbillableProjects extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{

		Schema::table('projects', function($table)
		{

			if (!Schema::hasColumn('projects', 'billable')) {
				$table->boolean('billable')->default(true);
				$table->index('billable');
			}
		});

		Schema::table('employees', function($table)
		{

			if (!Schema::hasColumn('employees', 'termination_date')) {
				$table->date('termination_date');
			}
		});

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
