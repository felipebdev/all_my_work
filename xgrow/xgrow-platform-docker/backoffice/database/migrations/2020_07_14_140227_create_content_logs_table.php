<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContentLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('content_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('route');
            $table->string('ip');
            $table->integer('user_id');
            $table->string('user_type');
            $table->uuid('platform_id');
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
        Schema::dropIfExists('content_logs');
    }
}
