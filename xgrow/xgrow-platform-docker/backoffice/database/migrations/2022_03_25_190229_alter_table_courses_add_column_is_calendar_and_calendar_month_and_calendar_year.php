<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableCoursesAddColumnIsCalendarAndCalendarMonthAndCalendarYear extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->boolean('is_calendar')->default(false)->after('active')->description('Enable LA Calendar');
            $table->integer('calendar_month')->default(1)->after('is_calendar')->description('Start calendar on month 1');
            $table->integer('calendar_year')->default(1)->after('calendar_month')->description('Start calendar on year 2022');

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
            $table->dropColumn('is_calendar');
            $table->dropColumn('calendar_month');
            $table->dropColumn('calendar_year');
        });
    }
}
