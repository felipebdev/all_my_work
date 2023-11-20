<?php

use App\Plan;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTablePlansSetInstallment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->integer('installment')->default(1)->change();
        });

        foreach (Plan::where('installment', '=', '0')->withTrashed()->get() as $cod=>$plan) {
            $plan->installment = 1;
            $plan->save();
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->integer('installment')->default(0)->change();
        });
    }
}
