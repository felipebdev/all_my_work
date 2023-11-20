<?php

use App\RoleCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoleCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('role_categories', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->string('name');
            $table->integer('order');
            $table->timestamps();
        });

        RoleCategory::create(['name' => 'Resumo', 'order' => 1]);
        RoleCategory::create(['name' => 'Produtos', 'order' => 2]);
        RoleCategory::create(['name' => 'Aprendizagem', 'order' => 3]);
        RoleCategory::create(['name' => 'Alunos', 'order' => 4]);
        RoleCategory::create(['name' => 'Vendas', 'order' => 5]);
        RoleCategory::create(['name' => 'Relatórios', 'order' => 6]);
        RoleCategory::create(['name' => 'Recursos', 'order' => 7]);
        RoleCategory::create(['name' => 'Configurações', 'order' => 8]);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('role_categories');
    }
}
