<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateRemainingPaymentPlanType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // update first payment of type = U to product
        $paymentPlans = DB::cursor("
             SELECT min(payment_plan.id) as id
             FROM payments
             LEFT JOIN payment_plan
                   ON payment_plan.payment_id = payments.id
                   where payments.`type` = 'U'
                   and payment_plan.`type` is null
                GROUP BY payments.order_code");

        foreach ($paymentPlans as $payment) {
            dump("Update U by payment_plan.id: " . $payment->id);
            DB::table('payment_plan')
                ->where('id', $payment->id)
                ->update(['type' => 'product']);
        }

        // update remaining payment of type = U to order_bump
        $payments = DB::cursor("
            SELECT payments.id as id
            from payments
            join payment_plan
                ON  payments.id = payment_plan.payment_id
                and payments.`type` = 'U'
                and payment_plan.`type` is null
                group by payments.id
        ");

        foreach ($payments as $payment) {
            dump("Update U by payment_plan.payment_id: " . $payment->id);
            DB::table('payment_plan')
                ->whereNull('type')
                ->where('payment_plan.payment_id', $payment->id)
                ->update(['type' => 'order_bump']);
        }

        // update all payments of type = R to product
        $payments = DB::cursor("
            SELECT payments.id as id
            from payments
            join payment_plan
                ON  payments.id = payment_plan.payment_id
                and payments.`type` = 'R'
                and payment_plan.`type` is null
                group by payments.id
        ");

        foreach ($payments as $payment) {
            dump("Update R by payment_plan.payment_id: " . $payment->id);
            DB::table('payment_plan')
                ->whereNull('type')
                ->where('payment_plan.payment_id', $payment->id)
                ->update(['type' => 'order_bump']);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
