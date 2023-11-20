<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColorsColumnsTablePlatformSiteConfigs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('platform_site_configs', function (Blueprint $table) {
            $table->string('login_primary_color')->nullable(); 
            $table->string('login_background_color')->nullable();

            $table->string('rodape_primary_color')->nullable(); 
            $table->string('rodape_background_color')->nullable();

            $table->string('cabecalho_primary_color')->nullable(); 
            $table->string('cabecalho_background_color')->nullable();

            $table->integer('welcome_template_id')->nullable();
            
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
            $table->removeColumn('login_primary_color');
            $table->removeColumn('login_background_color');

            $table->removeColumn('rodape_primary_color');
            $table->removeColumn('rodape_background_color');

            $table->removeColumn('cabecalho_primary_color');
            $table->removeColumn('cabecalho_background_color');

            $table->removeColumn('welcome_template_id');
        });
    }
}
