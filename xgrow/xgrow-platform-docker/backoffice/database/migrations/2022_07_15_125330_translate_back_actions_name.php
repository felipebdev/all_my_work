<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class TranslateBackActionsName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       DB::table('back_actions')->where('id', 1)->update(['name' => 'Criar']);
       DB::table('back_actions')->where('id', 2)->update(['name' => 'Editar']);
       DB::table('back_actions')->where('id', 3)->update(['name' => 'Excluir']);
       DB::table('back_actions')->where('id', 4)->update(['name' => 'Exportar']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('back_actions')->where('id', 1)->update(['name' => 'store']);
        DB::table('back_actions')->where('id', 2)->update(['name' => 'update']);
        DB::table('back_actions')->where('id', 3)->update(['name' => 'destroy']);
        DB::table('back_actions')->where('id', 4)->update(['name' => 'export']);
    }
}
