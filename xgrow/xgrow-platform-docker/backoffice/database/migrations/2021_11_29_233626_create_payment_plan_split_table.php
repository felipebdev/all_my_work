<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentPlanSplitTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_plan_split', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('client_id');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreignUuid('platform_id')->constrained('platforms');
            $table->foreignId('product_id')->constrained();
            $table->string('order_code');
            $table->foreignId('plan_id')->constrained();
            $table->foreignId('payment_plan_id')->constrained('payment_plan');
            $table->foreignId('producer_product_id')->constrained()->nullable();
            $table->float('percent')->nullable();
            $table->float('anticipation_value')->default(0);
            $table->string('type', 1)->comment('P: Produtor, C: Coprodutor, X: Xgrow');
            $table->timestamps();

            $table->index(['platform_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_plan_split');
    }
}
