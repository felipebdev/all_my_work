<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableCourseSubscribersAddColumnCerticatedAt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_subscribers', function (Blueprint $table) {
            $table->string('token')->nullable();
            $table->timestamp('certificated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('course_subscribers', function (Blueprint $table) {
            $table->dropColumn('token');
            $table->dropColumn('certificated_at');
        });
    }
}
