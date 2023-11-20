<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableProductsAddColumnUnlimitedDeliveryAndExternalLearningArea extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('unlimited_delivery')->default(true);
            $table->boolean('external_learning_area')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('unlimited_delivery');
            $table->dropColumn('external_learning_area');
        });
    }
}
