<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->float('percent_split')->nullable();
            $table->string('bank',3)->nullable();
            $table->string('branch',6)->nullable();
            $table->string('account',15)->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('percent_split');
            $table->dropColumn('bank');
            $table->dropColumn('branch');
            $table->dropColumn('account');
        });
    }
}
