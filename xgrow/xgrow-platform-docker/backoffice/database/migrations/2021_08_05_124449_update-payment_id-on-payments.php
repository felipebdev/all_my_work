<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdatePaymentIdOnPayments extends Migration
{
    /**
     * Migration that updates payments.payment_id. This update is required due to migration to "Charge ruler".
     * Old retry algorithm didn't save relation with the "original failed payment".
     * This migration sets payment_id = 0 (disabling FK check), skipping then from "Charge ruler"
     * Also, sets payment_id = null on failed payments at 2021-08-01 (last day before transition).
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        // Force old payments to be ignored in "Charge ruler"
        DB::table('payments')->whereNull('payment_id')->update(['payment_id' => 0]);

        // Payments at '2021-08-01' can be handled by "Charge ruler"
        DB::table('payments')
            ->where('payment_date', '=', '2021-08-01')
            ->where('status', '=', 'failed')
            ->update(['payment_id' => null]);

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('payments')
            ->where('payment_id', '=', 0)
            ->update(['payment_id' => null]);
    }
}
