<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscribersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscribers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('platform_id');
            $table->foreign('platform_id')->references('id')->on('platforms');
            $table->string('email')->unique();
            $table->string('name');
            $table->string('password');
            $table->longText('photo')->nullable();
            $table->string('main_phone')->nullable();
            $table->string('cel_phone')->nullable();            
            $table->string('type')->nullable();
            $table->string('tax_id_number')->nullable();
            $table->json('company_data')->nullable(); //company_name,tax_id_br_ie, tax_id_br_im            
            $table->date('birthday')->nullable();
            $table->string('gender', 15)->nullable();
            $table->string('status', 15)->nullable();
            $table->string('address_zipcode')->nullable();
            $table->string('address_street')->nullable();
            $table->string('address_number')->nullable();
            $table->string('address_city')->nullable();
            $table->string('address_country')->nullable();
            $table->longText('source_id')->nullable();
            $table->longText('oauth_google')->nullable();
            $table->longText('oauth_fb')->nullable();
            $table->dateTime('last_acess')->nullable();
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
        Schema::dropIfExists('subscribers');
    }
}
