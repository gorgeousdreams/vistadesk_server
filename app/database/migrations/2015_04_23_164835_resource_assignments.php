<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ResourceAssignments extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('resource_assignments', function($table)
		{
			$table->engine = 'InnoDB';
			$table->bigIncrements('id');
			$table->bigInteger('company_id')->unsigned();;
			$table->bigInteger('employee_id')->unsigned();;
			$table->integer('rate')->default(0);
			$table->integer('allocation')->default(100);
			$table->date('start_date');
			$table->date('end_date')->nullable();
			$table->string('description', 255);
			$table->index('start_date');
			$table->index('end_date');
			$table->index('employee_id');
			$table->index('company_id');
			$table->foreign('company_id')->references('id')->on('companies');
			$table->foreign('employee_id')->references('id')->on('employees');

		});

		DB::statement("delete from resource_rates where rate = 0");

		// Convert old resource_rates table
		DB::statement("insert into resource_assignments 
			select '', rr.company_id, e.id, rr.rate, 100, '2015-01-01', '2016-01-01', rr.description 
			from resource_rates rr, employees e 
			where e.resource_id = rr.resource_id 
			and e.id in 
			    (select distinct(timesheets.employee_id) from timesheets, timesheet_entries, projects 
			    	where timesheets.id = timesheet_entries.timesheet_id 
			    	and projects.id = timesheet_entries.project_id 
			    	and projects.company_id = rr.company_id)");	

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
