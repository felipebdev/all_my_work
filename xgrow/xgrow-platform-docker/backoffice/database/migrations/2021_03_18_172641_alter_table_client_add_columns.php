<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableClientAddColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->integer('image_id')->nullable();
            $table->string('holder_name')->nullable();
            $table->string('account_type')->nullable();
            $table->string('branch_check_digit')->nullable();
            $table->string('account_check_digit')->nullable();
            $table->string('phone_number')->nullable();
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
            $table->dropColumn('image_id');
            $table->dropColumn('holder_name');
            $table->dropColumn('account_type');
            $table->dropColumn('branch_check_digit');
            $table->dropColumn('account_check_digit');
            $table->dropColumn('phone_number');
        });
    }
}
