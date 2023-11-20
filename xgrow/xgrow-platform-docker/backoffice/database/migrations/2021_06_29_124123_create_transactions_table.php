<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('platform_id');
            $table->unsignedBigInteger('subscriber_id');
            $table->enum('status', ['failed', 'success'])->default('failed');
            $table->enum('type', ['credit_card', 'pix', 'bank_slip'])->default('credit_card');
            $table->string('transaction_id')->nullable();
            $table->string('transaction_code')->nullable();
            $table->string('transaction_message')->nullable();
            $table->decimal('total')->default(0.0);
            $table->unsignedBigInteger('credit_card_id')->nullable();
            $table->unsignedBigInteger('payment_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('platform_id')->references('id')->on('platforms')->onDelete('cascade');
            $table->foreign('subscriber_id')->references('id')->on('subscribers')->onDelete('cascade');
            $table->foreign('credit_card_id')->references('id')->on('credit_cards')->onDelete('cascade');
            $table->foreign('payment_id')->references('id')->on('payments')->onDelete('cascade');
        });

        Schema::create('transaction_plans', function (Blueprint $table) {
            $table->unsignedBigInteger('transaction_id');
            $table->unsignedBigInteger('plan_id');
            $table->enum('type', ['default', 'order_bump', 'upsell'])->default('default');
            $table->decimal('price')->default(0.0);

            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
            $table->foreign('plan_id')->references('id')->on('plans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction_plans');
        Schema::dropIfExists('transactions');
    }
}
