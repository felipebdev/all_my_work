<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTablePlansChangeDefaultCheckoutPayoutLimit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plans', function ($table) {
            $table->unsignedSmallInteger('checkout_payout_limit')->default(2)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plans', function ($table) {
            $table->unsignedSmallInteger('checkout_payout_limit')->default(null)->change();
        });
    }
}
