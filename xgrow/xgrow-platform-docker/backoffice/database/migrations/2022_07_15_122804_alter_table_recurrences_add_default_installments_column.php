<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableRecurrencesAddDefaultInstallmentsColumn extends Migration
{
    public function up()
    {
        Schema::table('recurrences', function (Blueprint $table) {
            $table->unsignedTinyInteger('default_installments')
                ->default(1)
                ->after('card_id')
                ->comment('Default number of installments to be used for this recurrence');
        });
    }

    public function down()
    {
        Schema::table('recurrences', function (Blueprint $table) {
            $table->dropColumn('default_installments');
        });
    }
}
