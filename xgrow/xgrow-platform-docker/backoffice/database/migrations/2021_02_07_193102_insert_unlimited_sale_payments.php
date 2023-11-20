<?php

use App\Payment;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;


class InsertUnlimitedSalePayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void5
     */
    public function up()
    {
        foreach( DB::table('recurrences')->where('type', 'U')->get() as $cod => $recurrence ) {
            $subscriber = DB::table('subscribers')->where('id', '=', $recurrence->subscriber_id)->first();
            $date = new Carbon\Carbon($recurrence->last_payment);
            $firstPayment = \App\Payment::where('platform_id', $subscriber->platform_id)->where('subscriber_id', $subscriber->id)->where('status', '=', \App\Payment::STATUS_PAID)->whereDate('payment_date', '=', $date->toDateString())->orderBy('payments.id', 'asc')->first();

            if( $firstPayment ) {
                //Change payment type
                $firstPayment->type = \App\Payment::TYPE_UNLIMITED;
                $firstPayment->installments = 1;
                $firstPayment->save();
            }

            //Insert pending payments
            for( $i = 1; $i <= $recurrence->total_charges-$recurrence->current_charge; $i ++ ) {
                $date = $date->addDays(30);
                if( $firstPayment ) {
                    $nexPayment = $firstPayment->replicate();
                    $nexPayment->payment_date = $date;
                    $nexPayment->status = \App\Payment::STATUS_PENDING;
                    $nexPayment->type = \App\Payment::TYPE_UNLIMITED;
                    $nexPayment->order_id = NULL;
                    $nexPayment->charge_id = NULL;
                    $nexPayment->order_code = NULL;
                    $nexPayment->charge_code = NULL;
                    $nexPayment->installments = 1;
                    $nexPayment->push();
                    dump('Adiconado vencimento '.$date->toDateString().' assinante '.$subscriber->id);
                }
            }

            //Delete recurrence type U
            DB::table('payment_recurrence')->where('recurrence_id', '=', $recurrence->id)->delete();
            DB::table('recurrences')->delete($recurrence->id);
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
