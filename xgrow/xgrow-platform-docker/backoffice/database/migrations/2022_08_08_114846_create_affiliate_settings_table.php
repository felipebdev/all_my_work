<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAffiliateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('affiliation_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->unique()->constrained();
            $table->foreign('product_id')->references('id')->on('products');
            $table->boolean('approve_request_manually')->default(false);
            $table->boolean('receive_email_notifications')->default(false);
            $table->boolean('buyers_data_access_allowed')->default(false);
            $table->string('support_email')->nullable();
            $table->longText('instructions')->nullable();
            $table->decimal('commission', 8, 2)->nullable();
            $table->enum('cookie_duration', ['0', '1', '30', '90', '180'])->default('0');
            $table->enum('assignment', ['last_click', 'first_click'])->default('last_click');
            $table->string('invite_link')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('affiliation_settings');
    }
}
