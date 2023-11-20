<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanResourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan_resources', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products');
            $table->unsignedBigInteger('product_plan_id')->nullable();
            $table->foreign('product_plan_id')->references('id')->on('plans');
            $table->unsignedBigInteger('plan_id')->nullable();
            $table->foreign('plan_id')->references('id')->on('plans');
            $table->uuid('platform_id');
            $table->foreign('platform_id')->references('id')->on('platforms');
            $table->string('type', 1);
            $table->integer('discount')->default(0);
            $table->longText('description')->nullable();
            $table->string('upsell_video_url')->nullable();
            $table->unsignedBigInteger('image_id')->default(0);
            $table->string('accept_event', 1)->nullable();
            $table->string('decline_event', 1)->nullable();
            $table->string('accept_url')->nullable();
            $table->string('decline_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plan_resources');
    }
}
