<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Role;

class CreateLiveRole extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Role::create(
            ['slug' => 'live', 'name' => 'Lives', 'role_category_id' => 3, 'order' => 9]
        );
        $role = new Role();
        $role->slug = 'live';
        $role->name = 'Lives';
        $role->role_category_id = 3;
        $role->order = 9;
        $role->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Role::where(['slug' => 'live', 'name' => 'Lives'])->delete();
    }
}
