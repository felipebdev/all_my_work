<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCourseColumnsColorsPlatformSiteConfigs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('platform_site_configs', function (Blueprint $table) {
            $table->string('course_primary_color')->nullable();
            $table->string('course_second_color')->nullable();
            $table->string('course_card_color')->nullable();
            $table->string('course_second_card_color')->nullable();
            $table->string('course_button_color')->nullable();
            $table->string('course_button_background')->nullable(); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('platform_site_configs', function (Blueprint $table) {
            $table->dropColumn('course_primary_color');
            $table->dropColumn('course_second_color');
            $table->dropColumn('course_card_color');
            $table->dropColumn('course_second_card_color');
            $table->dropColumn('course_button_color');
            $table->dropColumn('course_button_background');
        });
    }
}
