<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTablePaymentsAddType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->char('type', 1)->default(\App\Payment::TYPE_SALE);
        });

        //Update subscription plans
        foreach (\App\Payment::all() as $cod => $payment) {
            foreach( $payment->plans as $codPlan=>$plan ) {
                if( $plan->type_plan == \App\Plan::PLAN_TYPE_SUBSCRIPTION ) {
                    $payment->type = \App\Payment::TYPE_SUBSCRIPTION;
                    $payment->save();
                }
            }
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
