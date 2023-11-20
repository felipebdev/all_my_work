<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableOneClickAddClientIpUserAgentColumns extends Migration
{
    public function up()
    {
        Schema::table('one_click', function (Blueprint $table) {
            $table->ipAddress('client_ip_address')->nullable()->after('subscriber_id');
            $table->string('client_user_agent')->nullable()->after('client_ip_address');
        });
    }

    public function down()
    {
        Schema::table('one_click', function (Blueprint $table) {
            $table->dropColumn('client_ip_address');
            $table->dropColumn('client_user_agent');
        });
    }
}
