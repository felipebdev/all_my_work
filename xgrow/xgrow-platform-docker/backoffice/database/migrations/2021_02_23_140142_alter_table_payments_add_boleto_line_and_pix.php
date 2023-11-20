<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTablePaymentsAddBoletoLineAndPix extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->text('boleto_line')->nullable();
            $table->longText('pix_qrcode')->nullable();
            $table->text('pix_qrcode_url')->nullable();
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
            $table->dropColumn('boleto_line');
            $table->dropColumn('pix_qrcode');
            $table->dropColumn('pix_qrcode_url');
        });
    }
}
