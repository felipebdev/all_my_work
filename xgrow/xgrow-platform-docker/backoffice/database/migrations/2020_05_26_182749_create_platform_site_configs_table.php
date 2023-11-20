<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlatformSiteConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('platform_site_configs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('primary_color')->default('#246EE9');
            $table->string('secondary_color')->default('#E9ECEF');
            $table->string('background_color')->default('#FFF');
            $table->char('login_template',1)->default('C'); // Center // Right
//            $table->string('image_logo')->default('logo.png');
//            $table->string('image_template')->default('template.jpg');
            $table->uuid('platform_id');
            $table->foreign('platform_id')->references('id')->on('platforms');
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
        Schema::dropIfExists('platform_site_configs');
    }
}
