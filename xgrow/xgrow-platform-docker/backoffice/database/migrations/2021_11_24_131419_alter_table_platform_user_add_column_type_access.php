<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTablePlatformUserAddColumnTypeAccess extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('platform_user', function (Blueprint $table) {
            $table->enum('type_access', ['full', 'restrict'])->default('restrict')->after('platforms_users_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('platform_user', function (Blueprint $table) {
            $table->dropColumn('type_access');
        });
    }
}
