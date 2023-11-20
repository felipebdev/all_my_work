<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdatePaymentPlanType extends Migration
{
    public function up()
    {
        $paymentPlans = DB::cursor("
             SELECT min(payment_plan.id) as id
             FROM payments
             LEFT JOIN payment_plan
                   ON payment_plan.payment_id = payments.id
                   where payments.`type` = 'P'
                   and payment_plan.`type` is null
                GROUP BY payments.order_code");


        foreach ($paymentPlans as $payment) {
            dump("Update by payment_plan.id: " . $payment->id);
            DB::table('payment_plan')
                ->where('id', $payment->id)
                ->update(['type' => 'product']);
        }

        $payments = DB::cursor("
            SELECT payments.id as id
            from payments
            join payment_plan
                ON payments.id = payment_plan.payment_id
                and payments.`type` = 'P'
                and payment_plan.`type` is null
                group by payments.id
        ");

        foreach ($payments as $payment) {
            dump("Update P by payment_plan.payment_id: " . $payment->id);
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
