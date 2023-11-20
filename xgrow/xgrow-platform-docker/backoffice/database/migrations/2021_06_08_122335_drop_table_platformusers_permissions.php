<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropTablePlatformusersPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('platformusers_permissions');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('platformusers_permissions', function (Blueprint $table) {
            $table->uuid('platform_id');
            $table->unsignedBigInteger('platforms_users_id');
            $table->foreign('platform_id')->references('id')->on('platforms')->onDelete('cascade');
            $table->foreign('platforms_users_id')->references('id')->on('platforms_users')->onDelete('cascade');
        });
    }
}
