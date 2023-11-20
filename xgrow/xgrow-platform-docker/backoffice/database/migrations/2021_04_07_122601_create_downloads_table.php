<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDownloadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('downloads', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('status')->nullable();
            $table->string('period')->nullable();
            $table->text('filters')->nullable();
            $table->string('filename', 512)->nullable();
            $table->string('filesize')->default(0);
            $table->string('url', 512)->nullable();

            $table->uuid('platform_id');
            $table->foreign('platform_id')->references('id')->on('platforms');

            $table->unsignedBigInteger('platforms_users_id');
            $table->foreign('platforms_users_id')->references('id')->on('platforms_users');

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
        Schema::dropIfExists('downloads');
    }
}
