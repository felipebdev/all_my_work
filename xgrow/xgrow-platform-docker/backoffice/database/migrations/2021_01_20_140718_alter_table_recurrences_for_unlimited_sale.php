<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableRecurrencesForUnlimitedSale extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('recurrences', function (Blueprint $table) {
            $table->char('type', 1)->default('S');
            $table->unsignedInteger('total_charges')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('recurrences', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('total_charges');
        });
    }
}
