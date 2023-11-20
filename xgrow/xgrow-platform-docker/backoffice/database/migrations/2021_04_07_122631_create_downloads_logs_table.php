<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDownloadsLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('downloads_logs', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->uuid('platform_id');
            $table->foreign('platform_id')->references('id')->on('platforms');

            $table->unsignedBigInteger('platforms_users_id');
            $table->foreign('platforms_users_id')->references('id')->on('platforms_users');

            $table->unsignedBigInteger('downloads_id');
            $table->foreign('downloads_id')->references('id')->on('downloads');

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
        Schema::dropIfExists('downloads_logs');
    }
}
