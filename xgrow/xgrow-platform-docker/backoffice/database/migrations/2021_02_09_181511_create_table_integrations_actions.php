<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableIntegrationsActions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('integrations_actions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('platform_id');
            $table->uuid('integration_id');
            $table->uuid('integrations_actions_list_id');
            $table->string('status')->default('active');
            $table->string('trigger', 36);
            $table->string('description');
            $table->string('extra')->nullable();
            $table->foreign('platform_id')->references('id')->on('platforms');
            $table->foreign('integration_id')->references('id')->on('integrations');
            $table->foreign('integrations_actions_list_id')->references('id')->on('integrations_actions_list');
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
        Schema::dropIfExists('table_integrations_actions');
    }
}
