<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIntegrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('integrations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('id_webhook');
            $table->string('id_integration');
            $table->string('name_integration');
            $table->string('source_token');
            $table->integer('flag_enable');
            $table->string('url_webhook');            
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
        Schema::dropIfExists('integrations');
    }
}
