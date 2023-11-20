<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableCommentsAddColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('comments', function (Blueprint $table) {
//            $table->dropForeign('comments_contents_id_foreign');
//            $table->dropColumn('contents_id');
            $table->dropColumn('comment');

            $table->uuid('platform_id')->nullable();
            $table->foreign('platform_id')->references('id')->on('platforms');

//            $table->unsignedBigInteger('content_id')->nullable();
//            $table->foreign('content_id')->references('id')->on('contents');

            $table->unsignedBigInteger('subscriber_id')->nullable();
            $table->foreign('subscriber_id')->references('id')->on('subscribers');

            $table->longText('text')->nullable();
            $table->unsignedBigInteger('id_comment_sub')->nullable();


            $table->morphs('commentable');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('comments', function (Blueprint $table) {

            $table->dropForeign('comments_platform_id_foreign');
            $table->dropColumn('platform_id');

            $table->dropForeign('comments_subscriber_id_foreign');
            $table->dropColumn('subscriber_id');

            $table->dropColumn('text');
            $table->dropColumn('id_comment_sub');
            $table->dropColumn('commentable_id');
            $table->dropColumn('commentable_type');

            $table->unsignedBigInteger('contents_id')->nullable();
            $table->foreign('contents_id')->references('id')->on('contents');
            $table->string('comment');
        });
    }
}
