<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuthorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('authors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name_author')->nullable();
            $table->longText('author_photo_url')->nullable();
            $table->string('author_desc')->nullable();
            $table->string('author_email')->unique();
            $table->string('author_insta')->unique()->nullable();
            $table->string('author_linkedin')->unique()->nullable();
            $table->string('author_youtube')->unique()->nullable();
            $table->boolean('status')->default(1);
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
        Schema::dropIfExists('authors');
    }
}
