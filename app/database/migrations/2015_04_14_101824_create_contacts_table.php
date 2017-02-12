<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{

            Schema::create('contacts', function($table)
            {
                $table->engine = 'InnoDB';
                $table->bigIncrements('id');
                $table->bigInteger('profile_id')->unsigned();
                $table->string('name')->nullable();
                $table->string('phone')->nullable();
                $table->foreign('profile_id')->references('id')->on('profiles');
            });
           
            Schema::table('profiles', function($table)
            {
                $table->string('ssn')->nullable();
            });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
            Schema::dropIfExists('contacts');
            
            if (Schema::hasColumn('profiles', 'ssn')) {
                Schema::table('profiles', function($table)
                {
                    $table->dropColumn('ssn');
                });
            }
	}

}
