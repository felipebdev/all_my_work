<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCallcenterRestrictedIpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('callcenter_restricted_ip', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('callcenter_id')->unsigned();
            $table->string('ip_address');
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
        Schema::dropIfExists('callcenter_restricted_ip');
    }
}