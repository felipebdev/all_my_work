<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablePlatformusersPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('platformusers_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('platforms_users_id');
            $table->foreign('platforms_users_id')->references('id')->on('platforms_users')->onDelete('cascade');
            $table->uuid('platform_id');
            $table->foreign('platform_id')->references('id')->on('platforms')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('platformusers_permissions');
    }
}
