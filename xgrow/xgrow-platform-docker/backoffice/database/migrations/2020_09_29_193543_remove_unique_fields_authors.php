<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveUniqueFieldsAuthors extends Migration
{
     /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('authors', function (Blueprint $table) {

            $table->dropUnique('authors_author_insta_unique');
            $table->string('author_insta')->nullable()->change();

            $table->dropUnique('authors_author_linkedin_unique');
            $table->string('author_linkedin')->nullable()->change();

            $table->dropUnique('authors_author_youtube_unique');
            $table->string('author_youtube')->nullable()->change();
           
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('authors', function (Blueprint $table) {
            
        });
    }
}
