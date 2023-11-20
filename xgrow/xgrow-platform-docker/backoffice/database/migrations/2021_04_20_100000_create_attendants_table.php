<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendants', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('active');
            $table->boolean('allaudience');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->uuid('platform_id');
            $table->foreign('platform_id')->references('id')->on('platforms');
            $table->uuid('uuid');
            $table->string('api_token')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('attendants');
    }
}
