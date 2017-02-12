<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigrateWebdesk extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::update(DB::raw("update employees set comp_amount = comp_amount * 2080, comp_type = 'Annual' where full_time = 1 and comp_type is NULL"));
		DB::update(DB::raw("update employees set hire_date = '2014-01-01' where hire_date is NULL"));
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
