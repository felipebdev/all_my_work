<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBankInformationTable extends Migration
{
    public function up()
    {
        Schema::create('bank_information', function (Blueprint $table) {
            $table->id();
            $table->foreignId('platform_user_id')->constrained('platforms_users');
            $table->string('email');
            $table->char('document_type', 4);
            $table->string('document');
            $table->string('holder_name');
            $table->string('account_type');
            $table->string('bank', 3);
            $table->string('branch', 6);
            $table->string('account', 15);
            $table->string('branch_check_digit')->nullable();
            $table->string('account_check_digit');
            $table->integer('gateway_bank_id');
            $table->string('recipient_gateway')->default('mundipagg');
            $table->string('recipient_id')->nullable();
            $table->string('recipient_status')->default('pending');
            $table->boolean('used')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bank_information');
    }
}
