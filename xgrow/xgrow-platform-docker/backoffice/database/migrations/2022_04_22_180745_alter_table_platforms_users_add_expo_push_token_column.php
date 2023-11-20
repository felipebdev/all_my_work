<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTablePlatformsUsersAddExpoPushTokenColumn extends Migration
{
    public function up()
    {
        Schema::table('platforms_users', function (Blueprint $table) {
            $table->string('expo_push_token')->nullable();
        });
    }

    public function down()
    {
        Schema::table('platforms_users', function (Blueprint $table) {
            $table->dropColumn('expo_push_token');
        });
    }
}
