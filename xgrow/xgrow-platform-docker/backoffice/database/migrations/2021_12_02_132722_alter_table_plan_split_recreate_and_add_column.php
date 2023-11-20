<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTablePlanSplitRecreateAndAddColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_plan_split', function (Blueprint $table) {
            $table->dropForeign(['producer_product_id']);
            $table->dropColumn('producer_product_id');
        });

        Schema::table('payment_plan_split', function (Blueprint $table) {
            $table->foreignId('producer_product_id')->nullable()->constrained();
            $table->string('type', 1)
                ->comment('C: Produtor (Client), P: Coprodutor (Producer), X: Xgrow')
                ->change();
            $table->float('value')->after('percent');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_plan_split', function (Blueprint $table) {
            $table->dropColumn('value');
        });
    }
}
