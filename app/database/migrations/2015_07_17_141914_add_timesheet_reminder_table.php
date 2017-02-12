<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTimesheetReminderTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
            Schema::create('timesheet_reminders', function($table)
            {
                $table->engine = 'InnoDB';
                $table->bigIncrements('id');
                $table->bigInteger('tenant_id');
                $table->bigInteger('user_id');
                $table->integer('grace_period')->nullable();
                $table->mediumText('email_content')->nullable();
                $table->text('email_cc')->nullable();
                $table->timestamps();
            });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
            Schema::dropIfExists('timesheet_reminders');
	}

}
