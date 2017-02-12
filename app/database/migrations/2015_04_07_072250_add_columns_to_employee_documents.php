<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToEmployeeDocuments extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
            Schema::table('profiles', function($table)
            {
	      if (!Schema::hasColumn('profiles', 'filled'))
                $table->boolean('filled')->nullable();
	      if (!Schema::hasColumn('profiles', 'verified'))
                $table->boolean('verified')->nullable();
            });
            
            Schema::table('employee_documents', function($table)
            {
	      if (!Schema::hasColumn('employee_documents', 'filled'))
                $table->boolean('filled')->nullable();
	      if (!Schema::hasColumn('employee_documents', 'filled_at'))
                $table->dateTime('filled_at')->nullable();
	      if (!Schema::hasColumn('employee_documents', 'verified'))
                $table->boolean('verified')->nullable();
	      if (!Schema::hasColumn('employee_documents', 'verified_at'))
                $table->dateTime('verified_at')->nullable();
	      if (!Schema::hasColumn('employee_documents', 'signature'))
                $table->mediumText('signature')->nullable();
            });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
            Schema::table('profiles', function($table)
            {
                $table->dropColumn('filled');
                $table->dropColumn('verified');
            });
            
            Schema::table('employee_documents', function($table)
            {
                $table->dropColumn('filled');
                $table->dropColumn('filled_at');
                $table->dropColumn('verified');
                $table->dropColumn('verified_at');
                $table->dropColumn('signature');
            });
	}

}
