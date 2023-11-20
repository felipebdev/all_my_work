<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableCampaingAudience extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaign_audience', function (Blueprint $table) {
           $table->unsignedBigInteger('audience_id');
           $table->foreign('audience_id')->references('id')->on('audiences')->onDelete('cascade');
           $table->unsignedBigInteger('campaign_id');
           $table->foreign('campaign_id')->references('id')->on('campaigns')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('campaign_audience');
    }
}
