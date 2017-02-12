<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PermissionUpdates extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::dropIfExists('role_permissions');

		Schema::create('permission_assignments', function($table)
		{
			$table->engine = 'InnoDB';
			$table->bigIncrements('id');
			$table->bigInteger('role_id')->unsigned()->nullable();
			$table->bigInteger('user_id')->unsigned()->nullable();
			$table->bigInteger('permission_id')->unsigned();
			$table->index('role_id');
			$table->index('user_id');
			$table->index('permission_id');
			$table->foreign('role_id')->references('id')->on('roles');
			$table->foreign('user_id')->references('id')->on('users');
			$table->foreign('permission_id')->references('id')->on('permissions');
		});

		// Fix profiles, set last name = nullable
		DB::statement("ALTER TABLE profiles MODIFY last_name varchar(255);");

		// Fix roles table
		Schema::table('roles', function($table)
		{
			$table->bigInteger('tenant_id')->nullable()->default(1);
		});

		DB::statement("UPDATE roles set name = 'Admin' where name = 'admin'");
		DB::statement("UPDATE roles set name = 'Manager' where name = 'manager'");
		DB::statement("UPDATE roles set name = 'Finance' where name = 'finance'");

		// Create a template tenant to store the template roles 
		DB::statement("INSERT INTO tenants (id, name, address_id) values ('', 'Tenant Template', 1)");
		$tenantId = DB::table('tenants')->where('name', 'Tenant Template')->pluck('id');

		DB::statement("INSERT INTO profiles (first_name, last_name) values ('Tenant','Template')");
		$profileId = DB::table('profiles')->where('first_name', 'Tenant')->pluck('id');

		// And a template user so we can manage them
		DB::statement("INSERT INTO users (id, username, password, status, tenant_id, profile_id) values ('', 'template-user@ngdcorp.com', '".\Hash::make('vd-template-master')."', 1, $tenantId, $profileId);");

		$userId = DB::table('users')->where('tenant_id', $tenantId)->pluck('id');
		DB::statement("INSERT INTO roles (id, name, tenant_id) values ('', 'Admin', $tenantId);");
		$roleId = DB::table('roles')->where('tenant_id', $tenantId)->pluck('id');
		DB::statement("INSERT INTO role_user (role_id, user_id) values ($roleId, $userId);");

		DB::statement("INSERT INTO roles (id, name, tenant_id) values ('', 'Staff', $tenantId);");
		DB::statement("INSERT INTO roles (id, name, tenant_id) values ('', 'Accounting', $tenantId);");
		DB::statement("INSERT INTO roles (id, name, tenant_id) values ('', 'Human Resources', $tenantId);");
		DB::statement("INSERT INTO roles (id, name, tenant_id) values ('', 'Project Manager', $tenantId);");
		DB::statement("INSERT INTO roles (id, name, tenant_id) values ('', 'Timesheet Manager', $tenantId);");
		DB::statement("INSERT INTO roles (id, name, tenant_id) values ('', 'Client User', $tenantId);");
		DB::statement("INSERT INTO roles (id, name, tenant_id) values ('', 'Client Accounting', $tenantId);");

		// Add all of the starting permissions
		DB::statement("INSERT INTO `permissions` (id, type, resource, action) values " .
			// Client permissions
			"('', 'Service desk', 'company', 'view'), " .
			"('', 'Service desk', 'company', 'create'), " .

			"('', 'Service desk', 'company', 'edit'), " .
			"('', 'Service desk', 'company', 'delete'), " .

			"('', 'Service desk', 'project', 'view')," .
			"('', 'Service desk', 'project', 'create'), " .
			"('', 'Service desk', 'project', 'edit'), " .
			"('', 'Service desk', 'project', 'delete'), " .

			"('', 'Service desk', 'contract', 'view'), " .
			"('', 'Service desk', 'contract', 'create'), " .
			"('', 'Service desk', 'contract', 'edit'), " .
			"('', 'Service desk', 'contract', 'delete'), " .
			"('', 'Service desk', 'contract', 'approve'), " .

			"('', 'Service desk', 'budget', 'view'), " .
			"('', 'Service desk', 'budget', 'create'), " .
			"('', 'Service desk', 'budget', 'edit'), " .
			"('', 'Service desk', 'budget', 'delete'), " .
			"('', 'Service desk', 'budget', 'approve'), " .

			// Staff permissions
			"('', 'Human resources', 'employee', 'view'), " .
			"('', 'Human resources', 'employee', 'edit'), " .
			"('', 'Human resources', 'employee', 'create'), " .
			"('', 'Human resources', 'employee', 'delete'), " .

			"('', 'Human resources', 'working hours', 'view'), " .
			"('', 'Human resources', 'working hours', 'edit'), " .
			"('', 'Human resources', 'working hours', 'create'), " .
			"('', 'Human resources', 'working hours', 'delete'), " .

			"('', 'Human resources', 'pto', 'view'), " .
			"('', 'Human resources', 'pto', 'edit'), " .
			"('', 'Human resources', 'pto', 'create'), " .
			"('', 'Human resources', 'pto', 'delete'), " .

			"('', 'Human resources', 'document', 'view'), " .
			"('', 'Human resources', 'document', 'edit'), " .
			"('', 'Human resources', 'document', 'create'), " .
			"('', 'Human resources', 'document', 'delete'), " .
			"('', 'Human resources', 'document', 'approve'), " .

			// Financial permissions
			"('', 'Financial', 'finances', 'view'), " .
			"('', 'Financial', 'invoice', 'view'), " .
			"('', 'Financial', 'invoice', 'edit'), " .
			"('', 'Financial', 'invoice', 'create'), " .
			"('', 'Financial', 'payroll', 'view'), " .
			"('', 'Financial', 'accounting', 'view'), " .
			"('', 'Financial', 'account', 'view'), " .
			"('', 'Financial', 'account', 'edit'), " .
			"('', 'Financial', 'account', 'create'), " .
			"('', 'Financial', 'account', 'delete'), " .

			// Timesheet permissions
			"('', 'Timesheets', 'timesheet', 'approve'), " .
			"('', 'Timesheets', 'timesheet', 'create'), " .
			"('', 'Timesheets', 'timesheet', 'view');");
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
