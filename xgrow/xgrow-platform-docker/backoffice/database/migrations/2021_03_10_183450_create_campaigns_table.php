<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->integer('type');
            $table->boolean('has_start');
            $table->datetime('start_at')->nullable();
            $table->boolean('has_finish');
            $table->datetime('finish_at')->nullable();
            $table->integer('automatic_type')->nullable()->default(0);
            $table->integer('automatic_id')->nullable()->default(0);
            $table->integer('format');
            $table->string('subject')->nullable();
            $table->unsignedBigInteger('audio_id')->default(0);
            $table->text('text');
            $table->string('replyto')->nullable();
            $table->uuid('platform_id');
            $table->foreign('platform_id')->references('id')->on('platforms');
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
        Schema::dropIfExists('campaigns');
    }
}
