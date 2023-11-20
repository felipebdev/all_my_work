<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProducersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('producers', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('platform_id')->constrained('platforms');
            $table->foreignId('platform_user_id')->constrained('platforms_users');
            $table->boolean('accepted_terms')->default(false);
            $table->char('document_type', 4)->nullable();
            $table->string('document')->nullable();
            $table->string('holder_name')->nullable();
            $table->string('account_type')->nullable();
            $table->string('bank', 3)->nullable();
            $table->string('branch', 6)->nullable();
            $table->string('account', 15)->nullable();
            $table->string('branch_check_digit')->nullable();
            $table->string('account_check_digit')->nullable();
            $table->boolean('document_verified')->default(false);
            $table->string('recipient_id')->nullable();
            $table->string('recipient_gateway')->default('mundipagg');
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
        Schema::dropIfExists('producers');
    }
}
