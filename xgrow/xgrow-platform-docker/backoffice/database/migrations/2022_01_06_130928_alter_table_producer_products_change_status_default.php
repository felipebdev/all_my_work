<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableProducerProductsChangeStatusDefault extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('producer_products', function (Blueprint $table) {
            DB::statement("ALTER TABLE `producer_products` CHANGE `status` `status` ENUM('active','canceled', 'pending') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending';");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('producer_products', function (Blueprint $table) {
            DB::statement("ALTER TABLE `producer_products` CHANGE `status` `status` ENUM('active','canceled', 'pending') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active';");
        });
    }
}
