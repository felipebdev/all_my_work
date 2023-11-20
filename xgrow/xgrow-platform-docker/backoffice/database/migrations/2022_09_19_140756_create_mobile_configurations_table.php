<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMobileConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mobile_configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('platforms_users_id')->constrained('platforms_users');
            $table->boolean('notifications')->default(true);
            $table->boolean('notifications_sells')->default(true);
            $table->boolean('notifications_sells_product_name')->default(true);
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
        Schema::dropIfExists('mobile_configurations');
    }
}
