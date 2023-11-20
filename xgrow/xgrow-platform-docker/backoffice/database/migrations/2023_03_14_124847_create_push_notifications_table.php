<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePushNotificationsTable extends Migration
{
    public function up()
    {
        Schema::create('push_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('text');
            $table->timestamp('run_at');
            $table->foreignUuid('platform_id')->constrained();
            $table->boolean('is_sent')->default(0)->comment('Updated to true when sent to queue');
            $table->foreignId('user_id')->constrained('platforms_users')
                ->comment('User that created/changed notification');
            $table->enum('type', ['desktop', 'mobile'])->default('mobile');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('push_notifications');
    }
}
