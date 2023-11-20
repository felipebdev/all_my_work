<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTablePlansAddCheckoutColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->boolean('payment_method_boleto')->default(true);
            $table->boolean('payment_method_credit_card')->default(true);
            $table->boolean('payment_method_pix')->default(false);
            $table->boolean('payment_method_multiple_cards')->default(false);
            $table->integer('upsell_discount')->nullable();
            $table->text('upsell_message')->nullable();
            $table->integer('order_bump_discount')->nullable();
            $table->text('order_bump_message')->nullable();
            $table->string('checkout_whatsapp')->nullable();
            $table->string('checkout_email')->nullable();
            $table->string('checkout_support')->nullable();
            $table->string('checkout_facebook_pixel')->nullable();
            $table->string('checkout_google_tag')->nullable();
            $table->string('checkout_url_terms')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('integrations', function (Blueprint $table) {
            $table->dropColumn('payment_method_boleto');
            $table->dropColumn('payment_method_credit_card');
            $table->dropColumn('payment_method_pix');
            $table->dropColumn('payment_method_multiple_cards');
            $table->dropColumn('upsell_discount');
            $table->dropColumn('upsell_message');
            $table->dropColumn('order_bump_discount');
            $table->dropColumn('order_bump_message');
            $table->dropColumn('checkout_whatsapp');
            $table->dropColumn('checkout_email');
            $table->dropColumn('checkout_support');
            $table->dropColumn('checkout_support');
            $table->dropColumn('checkout_google_tag');
            $table->dropColumn('checkout_url_terms');
        });
    }
}
