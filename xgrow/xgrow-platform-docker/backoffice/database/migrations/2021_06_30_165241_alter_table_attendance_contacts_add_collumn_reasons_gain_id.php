<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableAttendanceContactsAddCollumnReasonsGainId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendance_contacts', function (Blueprint $table) {
            $table->unsignedBigInteger('reasons_gain_id')->after('reasons_loss_id')->nullable(); //somente quando ganho
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendance_contacts', function (Blueprint $table) {
            $table->dropColumn('reasons_gain_id'); 
        });
    }
}
