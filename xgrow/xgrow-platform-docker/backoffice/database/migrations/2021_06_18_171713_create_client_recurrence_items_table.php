<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientRecurrenceItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_recurrence_items', function (Blueprint $table) {
            $table->uuid('recurrence_id');
            $table->uuid('service_id');
            $table->decimal('price')->default(0.0);
            $table->timestamp('deleted_at')->nullable();
            $table->foreign('recurrence_id')->references('id')->on('client_recurrences')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_recurrence_items');
    }
}
