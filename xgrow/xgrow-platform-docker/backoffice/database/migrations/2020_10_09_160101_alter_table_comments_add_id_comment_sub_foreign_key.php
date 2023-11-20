<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableCommentsAddIdCommentSubForeignKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('comments', function (Blueprint $table) {
            $table->foreign('id_comment_sub')->references('id')->on('comments')
                    ->onDelete('cascade');
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign(['id_comment_sub']);
        });
        Schema::enableForeignKeyConstraints();
    }
}
