<?php

use App\BackRole;
use Illuminate\Database\Migrations\Migration;

class CreateBackofficeDataAcl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        BackRole::create(['slug' => 'dashboard', 'name' => 'Dashboard']);
        BackRole::create(['slug' => 'client', 'name' => 'Clientes']);
        BackRole::create(['slug' => 'subscriber', 'name' => 'Alunos']);
        BackRole::create(['slug' => 'platform', 'name' => 'Plataformas']);
        BackRole::create(['slug' => 'report', 'name' => 'Relatórios']);
        BackRole::create(['slug' => 'product', 'name' => 'Produtos']);
        BackRole::create(['slug' => 'setting', 'name' => 'Configurações']);
        BackRole::create(['slug' => 'user', 'name' => 'Usuários']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        BackRole::truncate();
    }
}
