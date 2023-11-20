<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableRecurrencesAddColumnPaymentMethodAndLastInvoice extends Migration
{
    public function up()
    {
        Schema::table('recurrences', function (Blueprint $table) {
            $table->string('payment_method')->default('credit_card')->after('type')->description('Prefered payment method');
            $table->dateTime('last_invoice')->nullable()->after('recurrence')->description('Date of the last recurrence invoice');
        });
    }

    public function down()
    {
        Schema::table('recurrences', function (Blueprint $table) {
            $table->dropColumn('payment_method');
            $table->dropColumn('last_invoice');
        });
    }
}
