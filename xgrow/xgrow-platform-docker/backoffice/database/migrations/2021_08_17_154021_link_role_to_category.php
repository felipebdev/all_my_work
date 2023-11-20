<?php

use App\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LinkRoleToCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->unsignedBigInteger('role_category_id')->nullable();
        });

        $roles = [
            ['slug' => 'dashboard', 'role_category_id' => 1],
            ['slug' => 'product', 'role_category_id' => 2],
            ['slug' => 'author', 'role_category_id' => 3],
            ['slug' => 'section', 'role_category_id' => 3],
            ['slug' => 'content', 'role_category_id' => 3],
            ['slug' => 'comment', 'role_category_id' => 3],
            ['slug' => 'course', 'role_category_id' => 3],
            ['slug' => 'forum', 'role_category_id' => 3],
            ['slug' => 'design', 'role_category_id' => 3],
            ['slug' => 'subscriber', 'role_category_id' => 4],
            ['slug' => 'financial', 'role_category_id' => 5],
            ['slug' => 'sale', 'role_category_id' => 5],
            ['slug' => 'subscription', 'role_category_id' => 5],
            ['slug' => 'lead', 'role_category_id' => 5],
            ['slug' => 'report', 'role_category_id' => 6],
            ['slug' => 'lists', 'role_category_id' => 6],
            ['slug' => 'engagement', 'role_category_id' => 7],
            ['slug' => 'callcenter', 'role_category_id' => 7],
            ['slug' => 'integration', 'role_category_id' => 7],
            ['slug' => 'config', 'role_category_id' => 8],
            ['slug' => 'email', 'role_category_id' => 8]
        ];

        foreach($roles as $role){
            $item = Role::where(['slug' => $role['slug']])->first();
            $item->update(['role_category_id' => $role['role_category_id']]);
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('roles', function (Blueprint $table) {
           $table->dropColumn('role_category_id');
        });
    }
}
