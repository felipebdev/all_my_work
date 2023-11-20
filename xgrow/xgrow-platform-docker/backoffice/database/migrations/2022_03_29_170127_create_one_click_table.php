<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOneClickTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('one_click', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('platform_id')->constrained();
            $table->foreignId('subscriber_id')->constrained();
            $table->string('payment_method');
            $table->string('card_id')->nullable();
            $table->integer('installments')->default(1);
            $table->datetime('expires_at');
            $table->datetime('locked_at')->nullable();
            $table->integer('tries')->default(0);
            $table->boolean('used')->default(false);
            $table->timestamps();

            $table->index(['platform_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('one_click');
    }
}
