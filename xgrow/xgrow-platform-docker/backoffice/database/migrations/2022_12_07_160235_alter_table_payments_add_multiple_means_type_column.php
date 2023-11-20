<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTablePaymentsAddMultipleMeansTypeColumn extends Migration
{
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('multiple_means_type', 16)->nullable()->after('multiple_means');
        });

        DB::statement("UPDATE payments SET multiple_means_type = 'c' WHERE multiple_means = 1");
    }

    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('multiple_means_type');
        });
    }
}
