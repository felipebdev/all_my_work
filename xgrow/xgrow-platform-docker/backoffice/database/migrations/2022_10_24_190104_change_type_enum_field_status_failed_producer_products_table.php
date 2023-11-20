<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTypeEnumFieldStatusFailedProducerProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE producer_products CHANGE COLUMN status status ENUM('active','canceled','pending','blocked','refused', 'failed') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE producer_products CHANGE COLUMN status status ENUM('active','canceled','pending','blocked','refused',) NOT NULL DEFAULT 'pending'");
    }
}
