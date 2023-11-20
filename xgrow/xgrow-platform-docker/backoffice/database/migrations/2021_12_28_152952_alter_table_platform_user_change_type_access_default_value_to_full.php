<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AlterTablePlatformUserChangeTypeAccessDefaultValueToFull extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('platform_user', function (Blueprint $table) {
             DB::statement("ALTER TABLE `platform_user` CHANGE `type_access` `type_access` ENUM('full','restrict') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'full';");
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
            DB::statement("ALTER TABLE `platform_user` CHANGE `type_access` `type_access` ENUM('full','restrict') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'restrict';");
        });
    }
}
