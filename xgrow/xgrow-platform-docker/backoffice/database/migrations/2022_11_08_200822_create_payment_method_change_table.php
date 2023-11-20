<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentMethodChangeTable extends Migration
{
    public function up()
    {
        Schema::create('payment_method_change', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained('payments');
            $table->string('origin')->nullable();
            $table->timestamps();
            $table->string('type_payment_old')->nullable();
            $table->string('type_payment_new')->nullable();
            $table->integer('installments_old')->nullable();
            $table->integer('installments_new')->nullable();
            $table->string('order_code_old')->nullable();
            $table->string('order_code_new')->nullable();
            $table->string('charge_id_old')->nullable();
            $table->string('charge_id_new')->nullable();
            $table->string('charge_code_old')->nullable();
            $table->string('charge_code_new')->nullable();
            $table->string('boleto_line_old')->nullable();
            $table->string('boleto_line_new')->nullable();
            $table->string('pix_qrcode_url_old')->nullable();
            $table->string('pix_qrcode_url_new')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('payment_method_change');
    }
}
