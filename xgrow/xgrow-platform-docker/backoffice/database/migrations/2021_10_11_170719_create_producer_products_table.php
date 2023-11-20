<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProducerProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('producer_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producer_id')->constrained('producers');
            $table->foreignId('product_id')->constrained('products');
            $table->date('contract_limit')->nullable();
            $table->float('percent')->nullable();
            $table->enum('status', ['active', 'canceled'])->default('active');
            $table->dateTime('canceled_at')->nullable();
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
        Schema::dropIfExists('producer_products');
    }
}
