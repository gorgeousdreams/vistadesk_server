<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVoidColumnToTheBillingEntries extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
            Schema::table('billing_entries', function($table)
            {
                $table->boolean('void')->nullable()->default(false);
            });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
            if (Schema::hasColumn('billing_entries', 'void')) {	
                    Schema::table('billing_entries', function($table)
                    {
                        $table->dropColumn('void');
                    });
            }
	}

}
