<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AppActionsNeverAccessed extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_actions_never_accessed', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('app_actions_id');
            $table->foreign('app_actions_id')->references('id')->on('app_actions')->onDelete('CASCADE');
            $table->unsignedBigInteger('subscriber_id');
            $table->foreign('subscriber_id')->references('id')->on('subscribers')->onDelete('CASCADE');
            $table->date('last_event');
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
        Schema::dropIfExists('app_actions_never_accessed');
    }
}
