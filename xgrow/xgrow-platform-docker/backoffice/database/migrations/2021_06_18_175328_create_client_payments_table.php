<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedInteger('client_id');
            $table->uuid('order_id')->nullable();
            $table->uuid('recurrence_id')->nullable();
            $table->string('code', 20)->unique();
            $table->string('status');
            $table->string('type')->default('credit_card');
            $table->string('transaction_code', 20)->nullable();
            $table->decimal('total')->default(0.0);
            $table->string('denial_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('order_id')->references('id')->on('client_orders')->onDelete('cascade');
            $table->foreign('recurrence_id')->references('id')->on('client_recurrences')->onDelete('cascade');
            $table->index('transaction_code');
            $table->index('code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_payments');
    }
}
