<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIssuesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('issues', function($table)
            {
                $table->engine = 'InnoDB';
                $table->bigIncrements('id');
                $table->bigInteger('external_id');
				$table->bigInteger('project_id');
				$table->string('pkey')->nullable();
				$table->string('summary')->nullable();
				$table->text('description')->nullable();
				$table->bigInteger('estimate')->nullable();
            });

		Schema::create('issue_history', function($table)
            {
                $table->engine = 'InnoDB';
                $table->bigIncrements('id');
                $table->bigInteger('issue_id');
				$table->string('created_by');
				$table->dateTime('created_at')->nullable();
				$table->enum('entry_type', array('New', 'ManagerApproved', 'ManagerByManager', 'ClientApproved', 'ClientRejected'))->default('New');
				$table->bigInteger('estimate');
				$table->text('notes')->nullable();
            });
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('issues');
		Schema::dropIfExists('issue_history');
	}

}
