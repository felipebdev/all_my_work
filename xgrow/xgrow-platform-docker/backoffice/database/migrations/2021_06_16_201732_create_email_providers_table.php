<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_providers', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name')->unique()
                ->comment('Provider identification name (preferable short, lowercase, only digits, no spaces)');
            $table->string('description')->nullable()->comment('Long description of provider');
            $table->string('from_name')->comment('From name used in provider');
            $table->string('from_address')->comment('From email address used in provider');
            $table->string('driver')->comment('Laravel driver name');
            $table->json('settings')->comment('JSON containing driver settings for provider');

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
        Schema::dropIfExists('email_providers');
    }
}
