<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpensesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('expenses', function($table)
            {
                $table->engine = 'InnoDB';
                $table->bigIncrements('id');
				$table->string('category')->nullable();
				$table->string('payment_type')->nullable();
				$table->string('memo')->nullable();
				$table->string('account')->nullable();
				$table->string('name')->nullable();
				$table->bigInteger('amount')->nullable();
                $table->dateTime('day')->nullable();
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
