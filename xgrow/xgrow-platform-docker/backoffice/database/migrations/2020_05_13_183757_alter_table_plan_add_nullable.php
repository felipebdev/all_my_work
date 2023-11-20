<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTablePlanAddNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->string('recurrence')->nullable()->change();
            $table->decimal('setup_price', 8, 2)->nullable()->change();
            $table->smallInteger('freedays')->nullable()->change();
            $table->string('freedays_type', 20)->nullable()->change();
            $table->smallInteger('charge_until')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plans', function (Blueprint $table) {
            //
        });
    }
}
