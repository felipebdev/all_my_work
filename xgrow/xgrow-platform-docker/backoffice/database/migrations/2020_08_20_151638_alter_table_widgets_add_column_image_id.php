<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableWidgetsAddColumnImageId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('widgets', function (Blueprint $table) {
            $table->unsignedBigInteger('image_id')->default(0);
            $table->unsignedBigInteger('icon_id')->default(0);
            $table->string('title')->nullable();
            $table->string('font')->nullable();
            $table->string('color')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('widgets', function (Blueprint $table) {
            $table->dropColumn('image_id');
            $table->dropColumn('icon_id');
            $table->dropColumn('title');
            $table->dropColumn('font');
            $table->dropColumn('color');
        });
    }
}
