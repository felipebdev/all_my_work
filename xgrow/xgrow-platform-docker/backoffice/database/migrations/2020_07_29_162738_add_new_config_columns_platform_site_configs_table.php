<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewConfigColumnsPlatformSiteConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('platform_site_configs', function (Blueprint $table) {
            $table->unsignedBigInteger('image_logo_login_id')->default(0);
            $table->unsignedBigInteger('image_logo_rodape_id')->default(0);
            $table->string('seo_title')->nullable();
            $table->string('seo_description')->nullable();
            $table->text('seo_keywords')->nullable();
            $table->text('copyright')->nullable();
            $table->integer('research_bar')->default(0);
            $table->integer('suport')->default(0);
            $table->integer('user_profile')->default(0);
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
            $table->dropColumn('image_logo_login_id');
            $table->dropColumn('image_logo_rodape_id');
            $table->dropColumn('seo_title');
            $table->dropColumn('seo_description');

            $table->dropColumn('seo_keywords');
            $table->dropColumn('copyright');
            $table->dropColumn('research_bar');
            $table->dropColumn('suport');
            $table->dropColumn('user_profile');
        });
    }
}
