<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableLeadAddOrderCorrelationIdColumn extends Migration
{
    
    public function up()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('order_correlation_id')->nullable()->comment('Correlation ID on order');
        });
    }

    public function down()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn('order_correlation_id');
        });
    }
}
