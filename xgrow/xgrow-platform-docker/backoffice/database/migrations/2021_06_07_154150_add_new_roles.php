<?php

use App\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Role::create(
            ['slug' => 'sale', 'name' => 'Vendas']
        );
        Role::create(
            ['slug' => 'subscription', 'name' => 'Assinaturas']
        );
        Role::create(
            ['slug' => 'lead', 'name' => 'Leads']
        );
        Role::create(
            ['slug' => 'email', 'name' => 'Emails']
        );
        Role::create(
            ['slug' => 'lists', 'name' => 'Listas Exportadas']
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
