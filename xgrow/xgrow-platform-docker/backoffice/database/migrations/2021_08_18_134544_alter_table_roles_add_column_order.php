<?php

use App\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableRolesAddColumnOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('roles', function (Blueprint $table) {
            $table->integer('order')->default(0);
        });


         $roles = [
            ['slug' => 'dashboard', 'order' => 1],
            ['slug' => 'product', 'order' => 1],
            ['slug' => 'coupons', 'order' => 2],
            ['slug' => 'section', 'order' => 1],
            ['slug' => 'course', 'order' => 2],
            ['slug' => 'content', 'order' => 3],
            ['slug' => 'comment', 'order' => 4],
            ['slug' => 'forum', 'order' => 5],
            ['slug' => 'author', 'order' => 6],
            ['slug' => 'transfer-content', 'order' => 7],
            ['slug' => 'design', 'order' => 8],
            ['slug' => 'subscriber', 'order' => 1],
            ['slug' => 'import-suscriber', 'order' => 2],
            ['slug' => 'financial', 'order' => 1],
            ['slug' => 'sale', 'order' => 2],
            ['slug' => 'subscription', 'order' => 3],
            ['slug' => 'lead', 'order' => 4],
            ['slug' => 'report', 'order' => 1],
            ['slug' => 'content-report', 'order' => 2],
            ['slug' => 'search-report', 'order' => 3],
            ['slug' => 'course-report', 'order' => 4],
            ['slug' => 'lists', 'order' => 5],
            ['slug' => 'integration', 'order' => 1],
            ['slug' => 'engagement', 'order' => 2],
            ['slug' => 'callcenter', 'order' => 3],
            ['slug' => 'config', 'order' => 1],
            ['slug' => 'user', 'order' => 2],
            ['slug' => 'permission', 'order' => 3],
            ['slug' => 'email', 'order' => 4],
            ['slug' => 'category', 'order' => 5]
        ];

        foreach($roles as $role){
            $item = Role::where(['slug' => $role['slug']])->first();
            $item->update(['order' => $role['order']]);
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
            $table->dropColumn('order');
        });
    }
}
