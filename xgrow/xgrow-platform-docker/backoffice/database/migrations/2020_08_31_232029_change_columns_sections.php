<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnsSections extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sections', function (Blueprint $table) {

            $table->integer('section_title')->nullable()->change();
            $table->integer('section_author')->nullable()->change();
            $table->integer('section_subtitle')->nullable()->change();
            $table->integer('section_description')->nullable()->change();
            $table->integer('section_qtd_per_page')->nullable()->change();

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
            //
        });
    }
}
