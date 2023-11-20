<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTablePlansAddOrderBumpAndCheckout extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->unsignedBigInteger('order_bump_plan_id')->nullable();
            $table->foreign('order_bump_plan_id')->references('id')->on('plans');
            $table->unsignedBigInteger('upsell_plan_id')->nullable();
            $table->foreign('upsell_plan_id')->references('id')->on('plans');
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
            $table->dropColumn('order_bump_plan_id');
            $table->dropColumn('upsell_plan_id');
        });
    }
}
