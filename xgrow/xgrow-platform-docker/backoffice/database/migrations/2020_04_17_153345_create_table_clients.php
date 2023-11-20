<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableClients extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email', 100)->unique();
            $table->string('password');
            $table->string('type_person',1)->nullable();
            $table->string('cpf',20)->nullable();
            $table->string('cnpj',20)->nullable();
            $table->string('fantasy_name')->nullable();
            $table->string('company_name')->nullable();
            $table->string('company_url')->nullable();
            $table->string('address',60)->nullable();
            $table->string('number',10)->nullable();
            $table->string('complement',20)->nullable();
            $table->string('district',30)->nullable();
            $table->string('city',40)->nullable();
            $table->string('state',2)->nullable();
            $table->string('zipcode',20)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::dropIfExists('clients');
    }
}
