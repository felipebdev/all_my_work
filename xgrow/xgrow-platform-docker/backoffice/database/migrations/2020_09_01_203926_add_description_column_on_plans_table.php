<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDescriptionColumnOnPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plans', function (Blueprint $table) {
            $message = "Este é um produto digital, você receberá os dados para acessá-lo via internet.";
            $table->longText('description')->nullable();
            $table->longText('message_success_checkout')->dafault($message);
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
            $table->dropColumn('description');
            $table->dropColumn('message_success_checkout');
        });
    }
}
