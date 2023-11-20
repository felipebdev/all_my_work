<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableSubscriptionsAddStatusColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscriptions', function ($table) {
            $table->string('status')->default('active')->after('gateway_transaction_id');
            $table->timestamp('status_updated_at')->useCurrent()->after('status');
        });

        // Set pre-existent
        \DB::statement("UPDATE subscriptions SET status_updated_at = created_at WHERE payment_pendent IS NULL AND canceled_at IS NULL");
        
        // Set pre-existent pending
        \DB::statement("UPDATE subscriptions SET status = 'pending', status_updated_at = payment_pendent WHERE payment_pendent IS NOT NULL");

        // Set pre-existent canceled
        \DB::statement("UPDATE subscriptions SET status = 'canceled', status_updated_at = canceled_at WHERE canceled_at IS NOT NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn(['status', 'status_updated_at']);
        });
    }
}
