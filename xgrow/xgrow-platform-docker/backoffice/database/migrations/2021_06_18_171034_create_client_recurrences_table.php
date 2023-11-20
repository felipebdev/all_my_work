<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientRecurrencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_recurrences', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedInteger('client_id');
            $table->uuid('service_id');
            $table->uuid('card_id');
            $table->string('code', 20)->unique();
            $table->unsignedInteger('recurrence')->default(30); //monthly
            $table->unsignedInteger('charges')->default(0);
            $table->dateTime('next_payment')->nullable();
            $table->dateTime('last_payment')->nullable();
            $table->decimal('total');
            $table->boolean('is_paid')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
            $table->foreign('card_id')->references('id')->on('client_credit_cards')->onDelete('cascade');
            $table->index('code');

            //todo: client_card_id
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_recurrences');
    }
}
