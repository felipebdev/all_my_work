<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableProducersAddRecipientStatusColumn extends Migration
{
    public function up()
    {
        Schema::table('producers', function (Blueprint $table) {
            $table->string('recipient_status')->default('registration')->after('recipient_id');
        });
    }

    public function down()
    {
        Schema::table('producers', function (Blueprint $table) {
            $table->dropColumn('recipient_status');
        });
    }
}
