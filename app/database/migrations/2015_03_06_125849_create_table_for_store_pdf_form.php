<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableForStorePdfForm extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
            
            
            	Schema::create('documents', function($table)
                {
                    $table->engine = 'InnoDB';
                    $table->bigIncrements('id');
                    $table->string('short_name', 50);
                    $table->string('full_name')->nullable();
                    $table->mediumText('description')->nullable();
                    $table->string('document_tpl_path')->nullable();
                    $table->integer('signature_page')->nullable();
                    $table->string('signature_css')->nullable();
                    $table->dateTime('created_at')->nullable();
                    $table->dateTime('updated_at')->nullable();
		});
                
                Schema::create('document_fields', function($table)
                {
                    $table->engine = 'InnoDB';
                    $table->bigIncrements('id');
                    $table->string('short_name', 50);
                    $table->string('full_name')->nullable();
                    $table->mediumText('description')->nullable();
                    $table->enum('type', array('text', 'email','phone','checkbox','select','number','date'));
                    $table->string('default_values')->nullable();
                    $table->string('validation')->nullable();
                    $table->string('mirror')->nullable(); // if needing field value presents in another table of database
                    $table->dateTime('created_at')->nullable();
                    $table->dateTime('updated_at')->nullable();
		});
                
                Schema::create('document_document_fields', function($table)
                {
                    $table->engine = 'InnoDB';
                    $table->bigIncrements('id');
                    $table->bigInteger('document_id');
                    $table->bigInteger('document_field_id');
                    $table->integer('document_page');
                    $table->string('css');
		});
                
                Schema::create('document_field_values', function($table)
                {
                    $table->engine = 'InnoDB';
                    $table->bigIncrements('id');
                    $table->bigInteger('document_field_id');
                    $table->bigInteger('employee_id');
                    $table->string('value');
                    $table->dateTime('created_at')->nullable();
                    $table->dateTime('updated_at')->nullable();
		});
                
                
                Schema::create('employee_documents', function($table)
                {
                    $table->engine = 'InnoDB';
                    $table->bigIncrements('id');
                    $table->bigInteger('employee_id');
                    $table->bigInteger('document_id');
		});
                
                Schema::table('employees', function($table)
                {
                    $table->string('documents')->nullable();
                    $table->mediumText('signature')->nullable();
                    //$table->date('termination_date')->nullable()->change();
                });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
               Schema::dropIfExists('documents');
               Schema::dropIfExists('document_fields');
               Schema::dropIfExists('document_document_fields');
               Schema::dropIfExists('employee_documents');
               Schema::dropIfExists('document_field_values');
               Schema::table('employees', function($table)
                {
                    $table->dropColumn('signature');
                    $table->dropColumn('documents');
                });

        }

}
