<?php

use App\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $roles = [
            ['slug' => 'coupons', 'role_category_id' => 2, 'name' => 'Cupons'],
            ['slug' => 'transfer-content', 'role_category_id' => 3, 'name' => 'Transferir Conteúdo'],
            ['slug' => 'import-suscriber', 'role_category_id' => 4, 'name' => 'Importar alunos'],
            ['slug' => 'content-report', 'role_category_id' => 6, 'name' => 'Conteúdos'],
            ['slug' => 'search-report', 'role_category_id' => 6, 'name' => 'Pesquisa'],
            ['slug' => 'course-report', 'role_category_id' => 6, 'name' => 'Cursos'],
            ['slug' => 'user', 'role_category_id' => 8, 'name' => 'Usuários'],
            ['slug' => 'permission', 'role_category_id' => 8, 'name' => 'Permissões'],
            ['slug' => 'category', 'role_category_id' => 8, 'name' => 'Agrupamentos']
        ];

        foreach($roles as $role){
            Role::create($role);
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
           'coupons',
           'transfer-content',
           'import-suscriber',
           'content-report',
           'search-report',
           'course-report',
           'learnig-area',
           'user',
           'permission',
           'category',
        ];

        foreach($roles as $role){
            $item = Role::where(['slug' => $role])->first();
            if($item)
                $item->delete();
        }
    }
}
