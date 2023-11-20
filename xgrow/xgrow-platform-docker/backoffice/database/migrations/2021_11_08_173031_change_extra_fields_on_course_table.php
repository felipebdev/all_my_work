<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeExtraFieldsOnCourseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->renameColumn('delivery_mode', 'is_experience');
            $table->dropColumn('diagram');
        });

        Schema::table('contents', function (Blueprint $table) {
            $table->string('category')->nullable();
        });

        Schema::table('modules', function (Blueprint $table) {
            $table->text('diagram')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->dropColumn('diagram');
        });

        Schema::table('contents', function (Blueprint $table) {
            $table->dropColumn('category');
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('is_experience');
        });
    }
}
