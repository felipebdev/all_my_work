<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableSubscriberAddCollumnAttendance extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('subscribers', function (Blueprint $table) {
            $table->unsignedBigInteger('attendant_id')->nullable();
            $table->foreign('attendant_id')->references('id')->on('attendants');
            $table->string('attendance_status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subscribers', function (Blueprint $table) {
            $table->dropColumn('attendant_id');
            $table->dropColumn('attendance_status');
        });
    }
}
