<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyUserTokensTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
            DB::statement("ALTER TABLE `user_tokens` MODIFY COLUMN `token_type` ENUM('remember','activation','api','pwreset','mimic');");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
            DB::statement("ALTER TABLE `user_tokens` MODIFY COLUMN `token_type` ENUM('remember','activation','api','pwreset');");
	}

}
