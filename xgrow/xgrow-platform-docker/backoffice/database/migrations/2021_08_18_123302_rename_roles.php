<?php

use App\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $roles = [
            ['slug' => 'report', 'name' => 'Acessos'],
            ['slug' => 'integration', 'name' => 'Integrações'],
            ['slug' => 'config', 'name' => 'Perfil plataforma']
        ];

        foreach($roles as $role){
            $item = Role::where(['slug' => $role['slug']])->first();
            $item->update(['name' => $role['name']]);
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $roles = [
            ['slug' => 'report', 'name' => 'Relatórios'],
            ['slug' => 'integration', 'name' => 'Recursos'],
            ['slug' => 'config', 'name' => 'Configurações']
        ];

        foreach($roles as $role){
            $item = Role::where(['slug' => $role['slug']])->first();
            $item->update(['name' => $role['name']]);
        }
    }
}
