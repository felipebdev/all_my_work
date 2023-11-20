<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableSubscriptionsAddClientIpAddresClientUserAgent extends Migration
{

    public function up()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->ipAddress('client_ip_address')->nullable()->after('order_number');
            $table->string('client_user_agent')->nullable()->after('client_ip_address');
        });
    }

    public function down()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('client_ip_address');
            $table->dropColumn('client_user_agent');
        });
    }
}
