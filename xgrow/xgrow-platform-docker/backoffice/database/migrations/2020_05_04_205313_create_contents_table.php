<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->boolean('published')->nullable();
            $table->dateTime('published_at')->nullable();
            $table->integer('thumb_small_id')->nullable();
            $table->integer('thumb_big_id')->nullable();
            $table->longText('content_html')->nullable();
            $table->boolean('has_audio_link')->nullable();
            $table->text('audio_link')->nullable();
            $table->boolean('has_video_link')->nullable();
            $table->text('video_link')->nullable();
            $table->boolean('has_external_link')->nullable();
            $table->text('external_link')->nullable();
            $table->text('hashtags')->nullable();
            $table->unsignedBigInteger('available_after_content_id')->nullable();
            $table->date('expire_in')->nullable();
            $table->unsignedBigInteger('section_id');
            $table->foreign('section_id')->references('id')->on('sections');
            $table->unsignedBigInteger('author_id');
            $table->foreign('author_id')->references('id')->on('authors');
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
        Schema::dropIfExists('contents');
    }
}
