<?php

use App\BackRole;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBackRolesRole extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        BackRole::create(['id' => 9, 'slug' => 'permissions', 'name' => 'Grupo de permissões']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $role = BackRole::find(9);
        $role->delete();
    }
}
