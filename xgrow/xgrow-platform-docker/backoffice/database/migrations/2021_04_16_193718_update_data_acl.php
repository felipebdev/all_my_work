<?php

use App\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDataAcl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        $roles = [
            ['dashboard','Resumo'],
            ['forum','Fórum'],
            ['engagement','Engajamento'],
            ['callcenter','Call Center'],
            ['product','Produtos'],
            ['financial','Financeiro'],
            ['integration','Recursos'],
        ];

        foreach ($roles as $role) {
 
            $item = Role::where('slug', $role[0])->first();
            $id = $item ? $item->id : 0;

            Role::updateOrCreate(
                    ['id' => $id],
                    ['slug' => $role[0], 'name' => $role[1]]
            );

        }

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
