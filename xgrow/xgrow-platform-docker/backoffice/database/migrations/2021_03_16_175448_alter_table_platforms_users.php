<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTablePlatformsUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('platforms_users', function (Blueprint $table) {
            $table->string('surname')->nullable();
            $table->string('display_name')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('instagram')->nullable();
            $table->string('facebook')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('platforms_users', function (Blueprint $table) {
            $table->dropColumn('surname');
            $table->dropColumn('display_name');
            $table->dropColumn('whatsapp');
            $table->dropColumn('linkedin');
            $table->dropColumn('instagram');
            $table->dropColumn('facebook');
        });
    }
}
