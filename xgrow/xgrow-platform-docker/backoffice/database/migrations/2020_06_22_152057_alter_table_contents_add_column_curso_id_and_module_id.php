<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableContentsAddColumnCursoIdAndModuleId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contents', function (Blueprint $table) {
             $table->boolean('is_course')->nullable();
             $table->unsignedBigInteger('course_id')->nullable();
             $table->unsignedBigInteger('module_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contents', function (Blueprint $table) {
           $table->dropColumn('course_id');
           $table->dropColumn('module_id');
        });
    }
}
