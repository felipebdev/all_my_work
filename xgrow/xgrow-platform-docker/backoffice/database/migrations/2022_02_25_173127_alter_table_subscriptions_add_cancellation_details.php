<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableSubscriptionsAddCancellationDetails extends Migration
{
    public function up()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('cancellation_reason', 100)->after('canceled_at')->nullable();

            $table->unsignedBigInteger('cancellation_user')->nullable();
            $table->foreign('cancellation_user')->references('id')->on('platforms_users');
        });
    }

    public function down()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('cancellation_reason');

            $table->dropForeign(['cancellation_user']);
            $table->dropColumn('cancellation_user');
        });
    }
}
