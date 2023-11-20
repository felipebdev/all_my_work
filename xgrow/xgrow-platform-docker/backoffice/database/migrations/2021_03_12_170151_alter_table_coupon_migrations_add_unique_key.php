<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableCouponMigrationsAddUniqueKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('coupon_mailings', function (Blueprint $table) {
            $table->dropForeign('coupon_mailings_coupon_id_foreign');
            $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('cascade');
            $table->unique(['coupon_id', 'email'], 'coupon_email_unique');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('coupon_mailings', function (Blueprint $table) {
            $table->dropForeign('coupon_mailings_coupon_id_foreign');
            $table->foreign('coupon_id')->references('id')->on('coupons');
            $table->dropUnique('coupon_email_unique');
        });
    }
}
