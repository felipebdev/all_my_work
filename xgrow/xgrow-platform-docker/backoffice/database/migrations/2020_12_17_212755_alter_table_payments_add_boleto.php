<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTablePaymentsAddBoleto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('boleto_barcode')->nullable();
            $table->string('boleto_qrcode')->nullable();
            $table->string('boleto_pdf')->nullable();
            $table->string('boleto_url')->nullable();
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
            $table->dropColumn('boleto_barcode');
            $table->dropColumn('boleto_qrcode');
            $table->dropColumn('boleto_pdf');
            $table->dropColumn('boleto_url');
        });
    }
}
