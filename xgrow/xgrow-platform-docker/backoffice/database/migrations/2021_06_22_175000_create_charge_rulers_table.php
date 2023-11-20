<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChargeRulersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('charge_rulers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('platform_id');
            $table->foreign('platform_id')->references('id')->on('platforms');
            $table->string('type');
            $table->boolean('active')->default(0);
            $table->integer('position');
            $table->integer('interval');
            $table->integer('email_id');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('charge_rulers');
    }
}
