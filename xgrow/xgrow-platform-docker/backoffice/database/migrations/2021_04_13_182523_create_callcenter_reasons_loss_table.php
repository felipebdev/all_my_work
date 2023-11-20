<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCallcenterReasonsLossTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('callcenter_reasons_loss', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('callcenter_id')->unsigned();
            $table->string('description');
            $table->foreign('callcenter_id')->references('id')->on('callcenter_config');
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
        Schema::dropIfExists('callcenter_reasons_loss');
    }
}
