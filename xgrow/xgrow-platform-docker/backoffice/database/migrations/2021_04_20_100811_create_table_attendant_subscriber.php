<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableAttendantSubscriber extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendant_subscriber', function (Blueprint $table) {
           $table->unsignedBigInteger('attendant_id');
           $table->foreign('attendant_id')->references('id')->on('attendants')->onDelete('cascade');
           $table->unsignedBigInteger('subscriber_id');
           $table->foreign('subscriber_id')->references('id')->on('subscribers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendant_subscriber');
    }
}
