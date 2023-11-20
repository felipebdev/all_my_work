<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablePaymentPlan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Create payment plan
        Schema::create('payment_plan', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->unsignedBigInteger('payment_id');
            $table->foreign('payment_id')->references('id')->on('payments');
            $table->unsignedBigInteger('plan_id');
            $table->foreign('plan_id')->references('id')->on('plans');
        });

        //Insert registers
        foreach (\App\Payment::all() as $cod => $payment) {
            if( strlen($payment->plan_id) > 0 ) {
                if( !$payment->plans()->where('plan_id', '=', $payment->plan_id)->exists() ) {
                    $payment->plans()->save(\App\Plan::findOrFail($payment->plan_id));
                }
            }
        }

        //Drop plan_id column
        /*Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign('payments_plan_id_foreign');
            $table->dropColumn('plan_id');
        });*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_plan');
    }
}
