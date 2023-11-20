<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableCouponsAddPlatform extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->unsignedBigInteger('platform_id')->nullable();
            $table->string('description')->nullable(true)->change();
            $table->unsignedBigInteger('plan_id')->nullable(true)->change();
            $table->dropForeign('coupons_plan_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn('platform_id');
            $table->string('description')->nullable(false)->change();
            $table->unsignedBigInteger('plan_id')->nullable(false)->change();
            $table->foreign('plan_id')->references('id')->on('plans');
        });
    }
}
