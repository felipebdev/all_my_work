<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMobileNotificationsTable extends Migration
{
    public function up()
    {
        Schema::create('mobile_notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->timestamps();
            $table->foreignUuid('platform_id')->constrained();
            $table->foreignId('platforms_users_id')->constrained('platforms_users');
            $table->string('title')->default('');
            $table->string('body')->default('');
            $table->boolean('read')->default(false);
        });
    }

    public function down()
    {
        Schema::dropIfExists('mobile_notifications');
    }
}
