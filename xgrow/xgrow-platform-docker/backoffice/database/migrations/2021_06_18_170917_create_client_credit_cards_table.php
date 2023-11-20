<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientCreditCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_credit_cards', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedInteger('client_id');
            $table->string('card_id');
            $table->string('brand');
            $table->string('last_four_digits');
            $table->string('holder_name');
            $table->string('exp_month');
            $table->string('exp_year');
            $table->boolean('is_default')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_credit_cards');
    }
}
