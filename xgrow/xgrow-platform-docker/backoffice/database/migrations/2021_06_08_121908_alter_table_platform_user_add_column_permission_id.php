<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTablePlatformUserAddColumnPermissionId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('platform_user', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id')->nullable();
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
            $table->dropColumn('permission_id');
        });
    }
}
