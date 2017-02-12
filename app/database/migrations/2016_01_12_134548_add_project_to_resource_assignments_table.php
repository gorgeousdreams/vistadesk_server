<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProjectToResourceAssignmentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('resource_assignments', function($table)
		{
			$table->bigInteger('project_id')->nullable()->default(null)->after('company_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		if (Schema::hasColumn('resource_assignments', 'project_id')) {
			Schema::table('resource_assignments', function($table)
			{
				$table->dropColumn('project_id');
			});
		}
	}

}
