<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('code', 7);
            $table->string('description');
            $table->date('maturity');
            $table->integer('occurrences')->default(0);
            $table->float('value');
            $table->char('value_type', 1)->default('V'); //V - Value P - Percent
            $table->integer('usage_limit')->nullable();
            $table->unsignedBigInteger('plan_id');
            $table->foreign('plan_id')->references('id')->on('plans');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedBigInteger('coupon_id')->nullable();
            $table->foreign('coupon_id')->references('id')->on('coupons');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign('payments_coupon_id_foreign');
            $table->dropColumn('coupon_id');
        });
        Schema::dropIfExists('coupons');
    }
}
