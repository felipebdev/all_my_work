<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('description')->nullable();
            $table->char('type')->default('R');
            $table->uuid('platform_id');
            $table->foreign('platform_id')->references('id')->on('platforms');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->foreign('category_id')->references('id')->on('plan_categories');
            $table->unsignedBigInteger('image_id')->default(0);
            $table->string('checkout_whatsapp')->nullable();
            $table->string('checkout_email')->nullable();
            $table->string('checkout_support')->nullable();
            $table->string('checkout_google_tag')->nullable();
            $table->string('checkout_url_terms')->nullable();
            $table->string('checkout_support_platform')->nullable();
            $table->string('checkout_layout')->nullable();
            $table->string('checkout_address')->nullable();
            $table->string('analysis_status', 15)->default('under_analysis');
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
        Schema::dropIfExists('products');
    }
}
