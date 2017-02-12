<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class JiraModule extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('modules', function($table)
		{
			$table->engine = 'InnoDB';
			$table->bigIncrements('id');
			$table->string('name');
			$table->string('description');
			$table->string('tablename');
			$table->bigInteger('display_price')->default(0);
		});

		Schema::create('module_instances', function($table)
		{
			$table->engine = 'InnoDB';
			$table->bigIncrements('id');
			$table->bigInteger('module_id')->unsigned()->nullable();
			$table->foreign('module_id')->references('id')->on('modules');
			$table->bigInteger('tenant_id')->unsigned()->nullable();
			$table->foreign('tenant_id')->references('id')->on('tenants');
			$table->boolean('enabled');
			$table->bigInteger('price')->default(0);
		});


		Schema::create('mod_jira', function($table)
		{
			$table->engine = 'InnoDB';
			$table->bigIncrements('id');
			$table->bigInteger('module_id')->unsigned()->nullable();
			$table->foreign('module_id')->references('id')->on('modules');
			$table->bigInteger('tenant_id')->unsigned()->nullable();
			$table->foreign('tenant_id')->references('id')->on('tenants');
			$table->string('host');
			$table->string('username');
			$table->string('password');
			$table->string('api_version');
			$table->enum('project_sync_method', array('Manual', 'Automatic'))->default('Manual');
			$table->enum('time_sync_method', array('Manual', 'Automatic'))->default('Manual');
			$table->enum('client_sync_method', array('Manual', 'Automatic'))->default('Manual');
			$table->string('client_locator')->default('Project Category');
		});

		Schema::create('mod_jira_projects', function($table)
		{
			$table->engine = 'InnoDB';
			$table->bigIncrements('id');
			$table->bigInteger('module_instance_id')->unsigned()->nullable();
			$table->foreign('module_instance_id')->references('id')->on('module_instances');
			$table->bigInteger('project_id')->unsigned();
			$table->foreign('project_id')->references('id')->on('projects');
			$table->string('jira_project_id');
		});


		// Create a template tenant to store the template roles 
		DB::statement("INSERT INTO modules (id, name, description, tablename, display_price) values ('', 'JIRA', 'Atlassian JIRA integration for timesheets, projects, and customers','mod_jira',0)");


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
