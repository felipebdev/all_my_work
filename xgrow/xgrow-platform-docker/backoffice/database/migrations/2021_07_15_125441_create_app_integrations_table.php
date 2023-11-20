<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppIntegrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apps', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('platform_id');
            $table->boolean('is_active')->default(false);
            $table->string('description')->nullable();
            $table->unsignedInteger('code');
            $table->string('type');
            $table->string('api_key')->nullable();
            $table->string('api_account')->nullable();
            $table->string('api_webhook')->nullable();
            $table->string('api_secret')->nullable();
            $table->text('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('platform_id')->references('id')->on('platforms')->onDelete('cascade');
        });

        Schema::create('app_actions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('app_id');
            $table->uuid('platform_id');
            $table->boolean('is_active')->default(false);
            $table->string('description')->nullable();
            $table->string('event');
            $table->string('action');
            $table->text('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('app_id')->references('id')->on('apps')->onDelete('cascade');
            $table->foreign('platform_id')->references('id')->on('platforms')->onDelete('cascade');
        });

        Schema::create('app_action_products', function (Blueprint $table) {
            $table->unsignedBigInteger('app_action_id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('plan_id')->nullable();

            $table->foreign('app_action_id')->references('id')->on('app_actions')->onDelete('cascade');
            $table->foreign('plan_id')->references('id')->on('plans')->onDelete('cascade');
            //TODO: when products created, create foreign key
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('app_action_products');
        Schema::dropIfExists('app_actions');
        Schema::dropIfExists('apps');
    }
}
