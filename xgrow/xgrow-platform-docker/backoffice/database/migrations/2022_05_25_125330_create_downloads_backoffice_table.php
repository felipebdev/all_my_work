<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDownloadsBackofficeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('downloads_backoffice', function (Blueprint $table) {
            $table->id();
            $table->string('status')->nullable();
            $table->string('period')->nullable();
            $table->text('filters')->nullable();
            $table->string('filename', 512)->nullable();
            $table->string('filesize')->default(0);
            $table->string('url', 512)->nullable();
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('downloads_backoffices');
    }
}
