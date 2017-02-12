<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
* BEFORE RUNNING THIS MIGRATION:
* 1. Export the webdesk database, both a full copy and a data-only copy:
* mysqldump -c -t --lock-tables=false webdesk > webdesk-data.sql
* 2. DROP THE DATABASE
* 3. Run the "schema.sql" file in sql/ folder
* 3. php artisan migrate --package="liebig/cron"
* 5. Run the "webdesk-data.sql" file created in step 1
* 6. Run the migrations: php artisan migrate
*/
class ProfileUpdates extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::dropIfExists('profiles');

		Schema::create('profiles', function($table)
		{
			$table->engine = 'InnoDB';
			$table->bigIncrements('id');
			$table->string('first_name', 255);
			$table->string('last_name', 255);
			$table->string('email', 320)->nullable();
			$table->bigInteger('address_id')->nullable()->unsigned();
			$table->date('date_of_birth')->nullable();
			$table->enum('gender', array('M', 'F'))->default('M');
			$table->bigInteger('image_id')->nullable()->unsigned();


			$table->foreign('address_id')->references('id')->on('addresses');
			$table->foreign('image_id')->references('id')->on('images');

			// apparently, these are created automatically by the InnoDB engine... 
			// http://dev.mysql.com/doc/refman/5.0/en/innodb-foreign-key-constraints.html
			$table->index('address_id');
			$table->index('image_id');
			$table->index('last_name');
			$table->index('first_name');

		});

		Schema::table('users', function($table)
		{

			if (!Schema::hasColumn('users', 'profile_id')) {
				$table->bigInteger('profile_id')->nullable()->unsigned();
				$table->foreign('profile_id')->references('id')->on('profiles');
				$table->index('profile_id');
			}
			$table->index('username');
		});

		Schema::table('employees', function($table)
		{
			if (Schema::hasColumn('employees', 'profile_image_id')) {
				$table->dropColumn('profile_image_id');
			}
			if (!Schema::hasColumn('employee', 'profile_id')) {
				$table->bigInteger('profile_id')->nullable()->unsigned();
				$table->foreign('profile_id')->references('id')->on('profiles');
			}
			$table->index('profile_id');
		});

		DB::insert(DB::raw("insert into profiles (first_name, last_name, email) select first_name, last_name, email from employees"));
		DB::update(DB::raw("update employees set profile_id = (select id from profiles where first_name COLLATE utf8_unicode_ci = employees.first_name COLLATE utf8_unicode_ci and last_name COLLATE utf8_unicode_ci = employees.last_name COLLATE utf8_unicode_ci);"));
		DB::update(DB::raw("update users u, employees e set u.profile_id = e.profile_id where e.user_id = u.id"));

		Schema::table('employees', function($table)
		{
			$table->dropColumn(array('first_name', 'last_name', 'email', 'user_id'));
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
