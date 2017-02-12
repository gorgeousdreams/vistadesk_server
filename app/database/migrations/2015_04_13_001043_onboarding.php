<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OnboardingMigration extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	/********* Migration with promlem on local machine !!!!!! ************/ 
	public function up()
	{
	 Schema::create('onboarding', function($table)
		{
			$table->engine = 'InnoDB';
			$table->bigIncrements('id');
			$table->bigInteger('employee_id');
			$table->boolean('basic_info')->default(false);
			$table->boolean('contact_info')->default(false);
		});
		
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('onboarding');
	}

}
