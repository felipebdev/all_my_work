<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableCoursesAddDeliveryFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->date('started_at')->nullable();
            $table->integer('form_delivery')->default(1);
            $table->integer('delivery_model')->nullable();
            $table->integer('delivery_date')->nullable();
            $table->date('delivered_at')->nullable();
            $table->integer('frequency')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('started_at');
            $table->dropColumn('form_delivery');
            $table->dropColumn('delivery_model');
            $table->dropColumn('delivery_date');
            $table->dropColumn('delivered_at');
            $table->dropColumn('days_after');
        });
    }
}
