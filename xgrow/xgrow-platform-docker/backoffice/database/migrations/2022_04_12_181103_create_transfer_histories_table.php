<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransferHistoriesTable extends Migration
{
    public function up()
    {
        Schema::create('transfer_histories', function (Blueprint $table) {
            $table->id();
            $table->datetime('event_at')->useCurrent();
            $table->foreignUuid('platform_id')->constrained();
            $table->foreignId('user_id')->nullable()->constrained('platforms_users');
            $table->string('recipient_id')->nullable();
            $table->bigInteger('amount')->nullable();
            $table->string('status')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transfer_histories');
    }
}
