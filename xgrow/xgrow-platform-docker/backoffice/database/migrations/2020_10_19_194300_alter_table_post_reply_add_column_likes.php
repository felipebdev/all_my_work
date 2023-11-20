<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTablePostReplyAddColumnLikes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('post_reply', function (Blueprint $table) {
            $table->bigInteger('likes')->default(0)->after('approved');
            $table->dropColumn('title');
            $table->dropColumn('tags');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('likes');
            $table->string('title', 50);
            $table->string('tags', 255);
        });
    }
}
