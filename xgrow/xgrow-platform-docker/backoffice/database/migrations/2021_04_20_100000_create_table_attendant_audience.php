<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableAttendantAudience extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendant_audience', function (Blueprint $table) {
           $table->unsignedBigInteger('attendant_id');
           $table->foreign('attendant_id')->references('id')->on('attendants')->onDelete('cascade');
           $table->unsignedBigInteger('audience_id');
           $table->foreign('audience_id')->references('id')->on('audiences')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendant_audience');
    }
}
