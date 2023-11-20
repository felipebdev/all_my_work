<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCallcenterConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('callcenter_config', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('active')->default(false);
            $table->boolean('period_restriction')->default(false);
            $table->date('initial_date')->nullable();
            $table->time('initial_hour')->nullable();
            $table->date('final_date')->nullable();
            $table->time('final_hour')->nullable();
            $table->boolean('ip_restriction')->default(false);
            $table->boolean('allow_changes')->default(false);
            $table->boolean('limit_leads')->default(false);
            $table->integer('number_leads')->nullable();
            $table->boolean('allow_reasons_loss')->default(false);
            $table->boolean('show_email')->default(false);
            $table->boolean('show_address')->default(false);
            $table->uuid('platform_id');
            $table->foreign('platform_id')->references('id')->on('platforms');
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
        Schema::dropIfExists('callcenter_config');
    }
}
