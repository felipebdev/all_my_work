<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIntegrationTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('integration_types', function (Blueprint $table) {
            $table->uuid('integration_id');
            $table->foreign('integration_id')->references('id')->on('integrations');

            $table->string('integration_type_id', 100)->nullable();

            $table->morphs('integratable');

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
        Schema::dropIfExists('integration_types');
    }
}
