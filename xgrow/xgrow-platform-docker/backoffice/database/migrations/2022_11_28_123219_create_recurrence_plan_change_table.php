<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecurrencePlanChangeTable extends Migration
{
    public function up()
    {
        Schema::create('recurrence_plan_change', function (Blueprint $table) {
            $table->id();
            $table->string('origin')->nullable();
            $table->foreignId('recurrence_id')->constrained('recurrences');
            $table->foreignId('old_plan_id')->constrained('plans');
            $table->foreignId('new_plan_id')->constrained('plans');
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('recurrence_plan_change');
    }
}
