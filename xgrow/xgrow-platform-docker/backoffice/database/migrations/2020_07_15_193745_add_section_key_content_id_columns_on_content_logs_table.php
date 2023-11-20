<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSectionKeyContentIdColumnsOnContentLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('content_logs', function (Blueprint $table) {
            $table->uuid('section_id')->nullable()->after('platform_id');
            $table->uuid('section_key')->nullable()->after('section_id');
            $table->unsignedBigInteger('content_id')->nullable()->after('section_key');
            $table->unsignedBigInteger('course_id')->nullable()->after('content_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('content_logs', function (Blueprint $table) {
            $table->dropColumn('section_id');
            $table->dropColumn('section_key');
            $table->dropColumn('content_id');
            $table->dropColumn('course_id');
        });
    }
}
