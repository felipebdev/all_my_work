<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlterTableRecurrenceUpdatePlanId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('recurrences', function (Blueprint $table) {

            //Update plan_id column
            foreach (DB::table('recurrences')->get() as $cod=>$recurrence) {
                $subscriber = DB::table('subscribers')->where('id', $recurrence->subscriber_id)->first();
                DB::table('recurrences')->where('id', $recurrence->id)->update(['plan_id'=>$subscriber->plan_id]);
            }

            //Remove nullable
            $table->unsignedBigInteger('plan_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('recurrences', function (Blueprint $table) {
            $table->dropColumn('plan_id');
        });
    }
}
