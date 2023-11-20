<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableOneClickAddPreviousIdColumn extends Migration
{
    public function up()
    {
        Schema::table('one_click', function (Blueprint $table) {
            $table->uuid('previous_id')->after('installments')->nullable();
            $table->foreign('previous_id')->references('id')->on('one_click');

        });
    }

    public function down()
    {
        Schema::table('one_click', function (Blueprint $table) {
            $table->dropForeign(['previous_id']);
            $table->dropColumn('previous_id');
        });
    }
}
