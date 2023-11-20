<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConfigColorsColumnsPlatformSiteConfigs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('platform_site_configs', function (Blueprint $table) {
            $table->string('second_background_color')->nullable(); 
            
            $table->string('search_background_color')->nullable();
            $table->string('search_color')->nullable(); 

            $table->string('button_color')->nullable();

            $table->string('cabecalho_secondary_color')->nullable(); 
            
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
            $table->dropColumn('second_background_color');
            $table->dropColumn('search_background_color');
            $table->dropColumn('search_color');
            $table->dropColumn('button_color');
            $table->dropColumn('cabecalho_secondary_color');
        });
    }
}
