<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterFkPostLikeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('post_like', function (Blueprint $table) {
            $table->dropForeign('post_like_post_id_foreign');
            $table->dropForeign('post_like_post_reply_id_foreign');
            $table->dropForeign('post_like_subscribers_id_foreign');
            $table->unsignedBigInteger('subscribers_id')->nullable()->change();
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            $table->foreign('post_reply_id')->references('id')->on('post_reply')->onDelete('cascade');
            $table->foreign('subscribers_id')->references('id')->on('subscribers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
