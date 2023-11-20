<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTablePlatformsUsersChangePlatformIdColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('platforms_users', function (Blueprint $table) {
            $table->dropForeign('platforms_users_platform_id_foreign');
            $table->dropIndex('platforms_users_platform_id_foreign');
        });

        Schema::table('platforms_users', function (Blueprint $table) {
            $table->string('platform_id')->nullable(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('platforms_users', function (Blueprint $table) {
            $table->string('platform_id')->nullable(false)->change();
            $table->foreign('platform_id')->references('id')->on('platforms');
        });
    }
}
