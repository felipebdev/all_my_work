<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTablePlansAddOrderBumpMediaFileColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->unsignedBigInteger('order_bump_image_id')->default(0);
            $table->unsignedBigInteger('upsell_image_id')->default(0);
            $table->unsignedBigInteger('uppsell_video_id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('order_bump_image_id');
            $table->dropColumn('upsell_image_id');
            $table->dropColumn('uppsell_video_id');
        });
    }
}
