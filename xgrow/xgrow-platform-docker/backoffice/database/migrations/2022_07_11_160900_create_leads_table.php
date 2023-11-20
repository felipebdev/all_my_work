<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadsTable extends Migration
{
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();

            $table->timestamps();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('cel_phone')->nullable();
            $table->string('document_type')->nullable();
            $table->string('document_number')->nullable();
            $table->string('address_zipcode')->nullable();
            $table->string('address_street')->nullable();
            $table->string('address_number')->nullable();
            $table->string('address_comp')->nullable();
            $table->string('address_district')->nullable();
            $table->string('address_city')->nullable();
            $table->string('address_state')->nullable();
            $table->string('address_country')->nullable();
            $table->foreignUuid('platform_id')->constrained();
            $table->foreignId('subscriber_id')->constrained();
            $table->foreignId('plan_id')->constrained();
            $table->string('cart_status')->default('initiated')
                ->comment('initiated, ordered, confirmed');
            $table->timestamp('cart_status_updated_at')->useCurrent();
            $table->string('type')->nullable()->comment('product, order_bump, upsell');
            $table->string('payment_method')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('leads');
    }
}
