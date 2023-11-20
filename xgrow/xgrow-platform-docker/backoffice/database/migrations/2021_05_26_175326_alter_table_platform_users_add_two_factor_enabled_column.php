<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTablePlatformUsersAddTwoFactorEnabledColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('platforms_users', function (Blueprint $table) {
            $table->boolean('two_factor_enabled')->default(false)->after('facebook');
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
            $table->dropColumn(['two_factor_enabled']);
        });
    }
}
