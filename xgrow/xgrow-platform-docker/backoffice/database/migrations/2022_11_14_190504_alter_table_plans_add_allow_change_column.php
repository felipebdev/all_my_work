<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTablePlansAddAllowChangeColumn extends Migration
{
    public function up()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->boolean('allow_change')->default(false)->after('product_id');
        });
    }

    public function down()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('allow_change');
        });
    }
}
