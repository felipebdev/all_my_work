<?php

use App\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDataAcl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Role::create(
            ['slug' => 'dashboard', 'name' => 'Dashboard']
        );
        Role::create(
            ['slug' => 'subscriber', 'name' => 'Assinantes']
        );
        Role::create(
            ['slug' => 'author', 'name' => 'Autores']
        );
        Role::create(
            ['slug' => 'section', 'name' => 'Seções']
        );
        Role::create(
            ['slug' => 'content', 'name' => 'Conteúdo']
        );
        Role::create(
            ['slug' => 'comment', 'name' => 'Comentários']
        );
        Role::create(
            ['slug' => 'course', 'name' => 'Cursos']
        );
        Role::create(
            ['slug' => 'report', 'name' => 'Relatórios']
        );
        Role::create(
            ['slug' => 'config', 'name' => 'Configurações']
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
