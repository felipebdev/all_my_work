<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableClientsAddDocumentValidationColumns extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('clients', function (Blueprint $table)
		{
			$table->string('phone2', 20)->nullable();
			$table->string('upload_directory', 150)->nullable();
			$table->string('check_document_number', 30)->nullable();
			$table->integer('check_document_type')->nullable();
			$table->tinyInteger('check_document_status')->nullable();
			$table->string('document_front_image_url', 300)->nullable();
			$table->string('document_back_image_url', 300)->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('clients', function (Blueprint $table)
		{
			$table->dropColumn('phone2');
			$table->dropColumn('upload_directory');
			$table->dropColumn('check_document_number');
			$table->dropColumn('check_document_type');
			$table->dropColumn('check_document_status');
			$table->dropColumn('document_front_image_url');
			$table->dropColumn('document_back_image_url');
		});
	}
}
