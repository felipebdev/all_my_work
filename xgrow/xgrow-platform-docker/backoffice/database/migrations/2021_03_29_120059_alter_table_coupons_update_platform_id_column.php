<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableCouponsUpdatePlatformIdColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('coupons', function (Blueprint $table) {
            $coupons = DB::table('coupons')->whereNull('maturity')->get();
            foreach ($coupons as $coupon) {
                DB::table('coupons')->where('id', $coupon->id)->update(['maturity' => date('Y-m-d')]);
            }

            $table->string('platform_id', 36)->nullable(false)->change();
        });

        Schema::table('coupons', function (Blueprint $table) {
            $coupons = DB::table('coupons')->get();
            foreach ($coupons as $coupon) {
                $platform = DB::table('platforms')->where('id', 'like', "%{$coupon->platform_id}%")->first();
                DB::table('coupons')->where('id', $coupon->id)->update(['platform_id' => $platform->id]);
            }

            $table->foreign('platform_id')->references('id')->on('platforms')->onDelete('cascade');
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
            $table->dropForeign('coupons_platform_id_foreign');
            $table->dropIndex('coupons_platform_id_foreign');
        });

        Schema::table('coupons', function (Blueprint $table) {
            $table->unsignedBigInteger('platform_id')->nullable(true)->change();

            $coupons = DB::table('coupons')->get();
            foreach ($coupons as $coupon) {
                DB::table('coupons')->where('id', $coupon->id)->update(['platform_id' => intval($coupon->platform_id)]);
            }
        });
    }
}
