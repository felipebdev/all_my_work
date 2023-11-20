<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableCoursesAddColumnRestrict extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->integer('restrict_date')->default(0);
            $table->integer('restrict_plan')->default(0);
            $table->date('restrict_start')->nullable();
            $table->date('restrict_finish')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('restrict_date');
            $table->dropColumn('restrict_plan');
            $table->dropColumn('restrict_start');
            $table->dropColumn('restrict_finish');
        });
    }
}
