<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTablePlatformsAddNotificationsWhatsappColumn extends Migration
{

    public function up()
    {
        Schema::table('platforms', function (Blueprint $table) {
            $table->boolean('notifications_whatsapp')->default(false)->after('google_tag_id');
        });
    }

    public function down()
    {
        Schema::table('platforms', function (Blueprint $table) {
            $table->dropColumn('notifications_whatsapp');
        });
    }
}
