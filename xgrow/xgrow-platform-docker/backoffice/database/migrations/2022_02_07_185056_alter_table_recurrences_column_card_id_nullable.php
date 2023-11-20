<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableRecurrencesColumnCardIdNullable extends Migration
{
    public function up()
    {
        Schema::table('recurrences', function (Blueprint $table) {
            $table->unsignedBigInteger('card_id')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('recurrences', function (Blueprint $table) {
            $table->unsignedBigInteger('card_id')->nullable(false)->change();
        });
    }
}
