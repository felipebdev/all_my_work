<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebhooksLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('webhooks_log', function (Blueprint $table) {
            $table->bigIncrements('id_transaction_log');
            $table->string('id_transaction');
            $table->dateTime('dt_hora');
            $table->longText('meta');
            $table->uuid('platform_id');
            $table->foreign('platform_id')->references('id')->on('platforms');        
        });
    }





    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('webhooks_log');
    }
}
