<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title', 50);
            $table->text('body');
            $table->string('tags', 255);
            $table->boolean('active')->default(0);
            $table->unsignedBigInteger('topic_id');
            $table->foreign('topic_id')->references('id')->on('topics');
            $table->unsignedBigInteger('forum_id');
            $table->foreign('forum_id')->references('id')->on('forums');
            $table->unsignedBigInteger('platforms_users_id')->nullable();
            $table->foreign('platforms_users_id')->references('id')->on('platforms_users');
            $table->unsignedBigInteger('subscribers_id')->nullable();
            $table->foreign('subscribers_id')->references('id')->on('subscribers');
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
        Schema::dropIfExists('posts');
    }
}
