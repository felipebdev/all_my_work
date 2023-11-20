<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableProducersAddRecipientReason extends Migration
{
    public function up()
    {
        Schema::table('producers', function (Blueprint $table) {
            $table->string('recipient_reason')->nullable()->after('recipient_status');
        });
    }

    public function down()
    {
        Schema::table('producers', function (Blueprint $table) {
            $table->dropColumn('recipient_reason');
        });
    }
}
