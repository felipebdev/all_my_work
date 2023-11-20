<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTablePlatformSiteConfigAddColumnStatusBackgroundImageLogin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('platform_site_configs', function (Blueprint $table) {
            $table->integer('status_background_image_login')->default(0); 
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
            $table->dropColumn('status_background_image_login');
        });
    }
}
