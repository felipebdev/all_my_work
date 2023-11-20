<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdatePaymentsCanceledToRefunded extends Migration
{

    public function up()
    {
        DB::table('payments')
            ->where('status', 'canceled')
            ->whereNotNull('charge_id')
            ->update(['status' => 'refunded']);
    }

    public function down()
    {
        DB::table('payments')
            ->where('status', 'refunded')
            ->update(['status' => 'canceled']);
    }
}
