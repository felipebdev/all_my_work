<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTablePaymentPlanSplitOrderCodeNullable extends Migration
{
    public function up()
    {
        Schema::table('payment_plan_split', function (Blueprint $table) {
            $table->string('order_code')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('payment_plan_split', function (Blueprint $table) {
            $table->string('order_code')->nullable(false)->change();
        });
    }
}
