<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTablePayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('gateway')->default('mundipagg');
            $table->string('order_id')->nullable();
            $table->string('charge_id')->nullable();
            $table->string('customer_id')->nullable();
            $table->unsignedBigInteger('subscription_id')->nullable()->change();
            $table->unsignedBigInteger('subscriber_id')->nullable();
            $table->foreign('subscriber_id')->references('id')->on('subscribers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('gateway');
            $table->dropColumn('order_id');
            $table->dropColumn('charge_id');
            $table->dropColumn('customer_id');
            $table->unsignedBigInteger('subscription_id')->change();
            $table->dropColumn('subscriber_id');
        });
    }
}
