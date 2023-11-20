<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConfigSectionColumnsTableSections extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sections', function (Blueprint $table) {
            $table->integer('content_title')->default(0);
            $table->integer('content_author')->default(0);
            $table->integer('content_subtitle')->default(0);
            $table->integer('content_description')->default(0);
            $table->integer('qtd_per_page')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sections', function (Blueprint $table) {
            $table->dropColumn('content_title');
            $table->dropColumn('content_author');
            $table->dropColumn('content_description');
            $table->dropColumn('qtd_per_page');
        });
    }
}
