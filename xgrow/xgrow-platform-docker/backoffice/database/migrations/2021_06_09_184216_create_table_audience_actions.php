<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableAudienceActions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audience_actions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('audience_id');
            $table->boolean('change_card');
            $table->boolean('resend_access_data');
            $table->boolean('resend_boleto');
            $table->string('link_pending')->nullable();
            $table->string('link_offer')->nullable();
            $table->timestamps();
            $table->foreign('audience_id')->references('id')->on('audiences')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('audience_actions');
    }
}
