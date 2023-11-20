<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImageBackgroundCollumnPlatformSiteConfig extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('platform_site_configs', function (Blueprint $table) {
            $table->unsignedBigInteger('background_image_id')->default(0);
            $table->integer('status_background_image')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('platform_site_configs', function (Blueprint $table) {
            $table->dropColumn('background_image');
            $table->dropColumn('status_background_image');
        });
    }
}
