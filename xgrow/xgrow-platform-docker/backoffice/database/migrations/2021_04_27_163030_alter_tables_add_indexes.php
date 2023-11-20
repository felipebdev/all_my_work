<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTablesAddIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscribers', function (Blueprint $table) {
            $table->index('email');
            $table->index('name');
            $table->index('document_number');
            $table->index('status');
        });

        Schema::table('plans', function (Blueprint $table) {
            $table->index('name');
            $table->index('type_plan');
            $table->index('status');
        });

        Schema::table('coupons', function (Blueprint $table) {
            $table->index('code');
            $table->index('value_type');
        });

        Schema::table('recurrences', function (Blueprint $table) {
            $table->index('recurrence');
            $table->index('order_number');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->index(['plan_id', 'subscriber_id']);
            $table->index('order_number');
            $table->index('gateway_transaction_id');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('charge_code');
            $table->index('order_code');
            $table->index('status');
            $table->index('type_payment');
            $table->index('type');
            $table->index('order_number');
            $table->index('payment_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subscribers', function (Blueprint $table) {
            $table->dropIndex('subscribers_email_index');
            $table->dropIndex('subscribers_name_index');
            $table->dropIndex('subscribers_document_number_index');
            $table->dropIndex('subscribers_status_index');
        });

        Schema::table('plans', function (Blueprint $table) {
            $table->dropIndex('plans_name_index');
            $table->dropIndex('plans_type_plan_index');
            $table->dropIndex('plans_status_index');
        });

        Schema::table('coupons', function (Blueprint $table) {
            $table->dropIndex('coupons_code_index');
            $table->dropIndex('coupons_value_type_index');
        });

        Schema::table('recurrences', function (Blueprint $table) {
            $table->dropIndex('recurrences_recurrence_index');
            $table->dropIndex('recurrences_order_number_index');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropIndex('subscriptions_plan_id_subscriber_id_index');
            $table->dropIndex('subscriptions_order_number_index');
            $table->dropIndex('subscriptions_gateway_transaction_id_index');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('payments_created_at_index');
            $table->dropIndex('payments_charge_code_index');
            $table->dropIndex('payments_order_code_index');
            $table->dropIndex('payments_status_index');
            $table->dropIndex('payments_type_payment_index');
            $table->dropIndex('payments_type_index');
            $table->dropIndex('payments_order_number_index');
            $table->dropIndex('payments_payment_date_index');
        });
    }
}
