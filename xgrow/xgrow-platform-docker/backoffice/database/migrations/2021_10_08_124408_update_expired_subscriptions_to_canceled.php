<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateExpiredSubscriptionsToCanceled extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = DB::table('subscriptions');
        $sql->join('payments', 'subscriptions.order_number', '=', 'payments.order_number');
        $sql->where('payments.status', 'expired');
        $sql->whereIn('payments.type_payment', ['boleto', 'pix']);
        $sql->where('subscriptions.status', 'pending_payment');
        $sql->update(['subscriptions.status' => 'canceled','subscriptions.status_updated_at' => now(),'subscriptions.canceled_at' => Carbon::now()]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
