<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPrimaryKeyColumnOnGetnetChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('getnet_charges', function (Blueprint $table) {
            $table->dropPrimary();
            $table->string('charge_id')->unique()->change();
//            $table->bigIncrements('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('getnet_charges', function (Blueprint $table) {

//            $table->dropPrimary('id');
            $table->string('charge_id')->primary()->change();
            $table->dropUnique('getnet_charges_charge_id_unique');
//            $table->dropColumn('id');
        });
    }
}
