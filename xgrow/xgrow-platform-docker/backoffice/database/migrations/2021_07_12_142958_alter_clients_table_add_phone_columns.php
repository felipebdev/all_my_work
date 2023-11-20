<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterClientsTableAddPhoneColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('phone_country_code', 4)->nullable();
            $table->string('phone_area_code', 4)->nullable();
            $table->string('phone_number_code', 11)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('phone_country_code');
            $table->dropColumn('phone_area_code');
            $table->dropColumn('phone_number_code');
        });
    }
}
