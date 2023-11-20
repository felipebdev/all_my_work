<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTablePaymentPlanAddColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_plan', function (Blueprint $table) {
            $table->float('tax_value')->nullable();
            $table->float('plan_value')->nullable();
            $table->float('plan_price')->nullable();
            $table->unsignedBigInteger('coupon_id')->nullable();
            $table->foreign('coupon_id')->references('id')->on('coupons');
            $table->string('coupon_code', 7)->nullable();
            $table->float('coupon_value')->nullable();
            $table->char('type')->nullable();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->boolean('multiple_means')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_plan', function (Blueprint $table) {
            $table->dropColumn('tax_value');
            $table->dropColumn('plan_value');
            $table->dropColumn('plan_price');
            $table->dropIndex('payment_plan_coupon_id_foreign');
            $table->dropColumn('coupon_id');
            $table->dropColumn('coupon_code');
            $table->dropColumn('coupon_value');
            $table->dropColumn('type');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('multiple_means');
        });
    }
}
