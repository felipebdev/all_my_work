<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableCallcenterConfigAddColumnAlowReasonsGain extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('callcenter_config', function (Blueprint $table) {
            $table->boolean('allow_reasons_gain')->default(false)->after('allow_reasons_loss');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('callcenter_config', function (Blueprint $table) {
            $table->dropColumn('allow_reasons_gain');
        });
    }
}
