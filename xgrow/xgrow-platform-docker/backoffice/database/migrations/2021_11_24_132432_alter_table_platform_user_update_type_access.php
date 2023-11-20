<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AlterTablePlatformUserUpdateTypeAccess extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('platform_user', function (Blueprint $table) {
            DB::table('platform_user')->whereNull('permission_id')->update(['type_access' => 'full']);
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
            DB::table('platform_user')->update(['type_access' => 'restrict']);
        });
    }
}
