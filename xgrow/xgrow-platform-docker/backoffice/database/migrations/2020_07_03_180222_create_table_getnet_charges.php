<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableGetnetCharges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('getnet_charges', function (Blueprint $table) {
            $table->string("charge_id")->primary();
            $table->string("seller_id")->nullable();
            $table->string("subscription_id")->nullable();
            $table->string("customer_id")->nullable();
            $table->string("plan_id")->nullable();
            $table->string("payment_id")->nullable();
            $table->decimal("amount", 10, 2)->nullable();
            $table->string("status")->nullable();
            $table->date("scheduled_date")->nullable();
            $table->date("create_date")->nullable();
            $table->integer("retry_number")->nullable();
            $table->date("payment_date")->nullable();
            $table->string("payment_type")->nullable();
            $table->string("terminal_nsu")->nullable();
            $table->string("authorization_code")->nullable();
            $table->string("acquirer_transaction_id")->nullable();
            $table->integer("installment")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('getnet_charges');
    }
}
