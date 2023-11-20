<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableBankInformationAddRecipientReason extends Migration
{
    public function up()
    {
        Schema::table('bank_information', function (Blueprint $table) {
            $table->string('recipient_reason')->nullable()->after('recipient_status');
        });
    }

    public function down()
    {
        Schema::table('bank_information', function (Blueprint $table) {
            $table->dropColumn('recipient_reason');
        });
    }
}
