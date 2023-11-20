<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_cards', function (Blueprint $table) {
            $table->uuid('platform_id');
            $table->foreign('platform_id')->references('id')->on('platforms');
            $table->unsignedBigInteger('course_id');
            $table->foreign('course_id')->references('id')->on('courses');
            $table->unsignedBigInteger('subscriber_id');
            $table->foreign('subscriber_id')->references('id')->on('subscribers');
            $table->uuid('payment_id')->primary();
            $table->uuid('seller_id');
            $table->decimal('amount');
            $table->char('currency', 5)->default('BRL');
            $table->uuid('order_id');
            $table->string('status');
            $table->dateTime('received_at');
            $table->boolean('credit_delayed');
            $table->string('credit_authorization_code');
            $table->dateTime('credit_authorized_at');
            $table->string('credit_reason_code');
            $table->string('credit_reason_message');
            $table->string('credit_acquirer');
            $table->string('credit_soft_descriptor');
            $table->string('credit_brand');
            $table->string('credit_terminal_nsu');
            $table->string('credit_acquirer_transaction_id');
            $table->string('credit_transaction_id');
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
        Schema::dropIfExists('payment_cards');
    }
}
