<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** @package  */
class AlterTableCourseProductAddContentCourseIdField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_product', function (Blueprint $table) {
            $table->unsignedBigInteger('course_id')->nullable()->change();
            $table->string('content_course_id', 42)->nullable()->after('product_id')->comment('Used for ID retrived by content api GRAPHQL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('section_product', function (Blueprint $table) {
            $table->unsignedBigInteger('course_id')->nullable()->change();
            $table->dropColumn('content_course_id');
        });
    }
}
