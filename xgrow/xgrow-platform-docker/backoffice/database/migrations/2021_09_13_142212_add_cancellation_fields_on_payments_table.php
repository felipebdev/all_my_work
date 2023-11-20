<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCancellationFieldsOnPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('cancellation_reason', 100)->nullable();
            $table->timestamp('cancellation_at')->nullable();
            $table->unsignedBigInteger('cancellation_user')->nullable();
            $table->foreign('cancellation_user')->references('id')->on('platforms_users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('cancellation_reason');
            $table->dropColumn('cancellation_at');
            $table->dropColumn('cancellation_user');
            $table->dropForeign('payments_cancellation_user');
        });
    }
}
