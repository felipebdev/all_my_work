<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTablePlansAddPromotionalPrice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->boolean('use_promotional_price')->default(false);
            $table->unsignedDecimal('promotional_price')->nullable();
            $table->unsignedInteger('promotional_periods')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('use_promotional_price');
            $table->dropColumn('promotional_price');
            $table->dropColumn('promotional_periods');
        });
    }
}
