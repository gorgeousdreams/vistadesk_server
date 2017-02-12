<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePtoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
            Schema::create('pay_time_off', function($table)
            {
                $table->engine = 'InnoDB';
                $table->bigIncrements('id');
                $table->bigInteger('employee_id');
                $table->dateTime('start_date');
                $table->dateTime('end_date');
                $table->integer('hours');
                $table->text('comment');
                $table->enum('status', array('Open','Closed','Approved','Rejected','Pending'));
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
            Schema::dropIfExists('pay_time_off');
	}

}
