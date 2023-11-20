<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConfigSectionColumnsOnSectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sections', function (Blueprint $table) {
            $table->integer('section_title')->default(0);
            $table->integer('section_author')->default(0);
            $table->integer('section_subtitle')->default(0);
            $table->integer('section_description')->default(0);
            $table->integer('section_qtd_per_page')->default(0);
            $table->bigInteger('section_template_id')->default(1);
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
            $table->dropColumn('section_title');
            $table->dropColumn('section_author');
            $table->dropColumn('section_subtitle');
            $table->dropColumn('section_description');
            $table->dropColumn('section_qtd_per_page');
            $table->dropColumn('section_template_id');
        });
    }
}
