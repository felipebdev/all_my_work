<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateCacheEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cache_entries', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name')->unique()->comment('Identification name (Redis keys convention)');
            $table->string('description')->nullable()->comment('Long description of setting');
            $table->string('default_value')->comment('Default value on application boot/cold start');

            $table->timestamps();
        });

        DB::table('cache_entries')->insert([
            'name' => 'MAIL_PROVIDER_NAME',
            'description' => 'Provedor de e-mail utilizado',
            'default_value' => 'log',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cache_entries');
    }
}
