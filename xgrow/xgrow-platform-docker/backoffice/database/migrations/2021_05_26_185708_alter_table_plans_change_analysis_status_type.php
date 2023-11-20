<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlterTablePlansChangeAnalysisStatusType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->string('analysis_status', 15)->default('under_analysis')->change();
        });

        DB::table('plans')->where('analysis_status', '=', '1')->update(['analysis_status' => 'approved']);
        DB::table('plans')->where('analysis_status', '=', '0')->update(['analysis_status' => 'under_analysis']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
