<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTablePlatformSiteConfigaddColumnsImages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('platform_site_configs', function (Blueprint $table) {
            $table->integer('image_logo_id')->nullable();
            $table->integer('image_template_id')->nullable();
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
            $table->removeColumn('image_logo_id');
            $table->removeColumn('image_template_id');
        });
    }
}
