<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTransactionsTableAddOrderCodeColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign('transactions_credit_card_id_foreign');
            $table->dropColumn('credit_card_id');
            $table->string('order_code')->after('subscriber_id')->nullable();
            $table->string('card_id')->after('total')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('card_id');
            $table->dropColumn('order_code');
            $table->unsignedBigInteger('credit_card_id')->after('total')->nullable();
            $table->foreign('credit_card_id')->references('id')->on('credit_cards')->onDelete('cascade');
        });
    }
}
