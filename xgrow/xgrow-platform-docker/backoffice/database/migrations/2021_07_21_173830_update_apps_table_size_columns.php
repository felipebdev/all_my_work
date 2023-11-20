<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAppsTableSizeColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('apps', function (Blueprint $table) {
            $table->text('api_key')->nullable()->change();
            $table->text('api_account')->nullable()->change();
            $table->text('api_webhook')->nullable()->change();
            $table->text('api_secret')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('apps', function (Blueprint $table) {
            $table->string('api_key')->nullable()->change();
            $table->string('api_account')->nullable()->change();
            $table->string('api_webhook')->nullable()->change();
            $table->string('api_secret')->nullable()->change();
        });
    }
}
