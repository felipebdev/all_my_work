<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTablePlanResourcesRenameColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up()
    {
        Schema::table('plan_resources', function (Blueprint $table) {
             $table->renameColumn('description', 'message');
             $table->renameColumn('upsell_video_url', 'video_url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plan_resources', function (Blueprint $table) {
             $table->renameColumn('message', 'description');
             $table->renameColumn('video_url', 'upsell_video_url');
        });
    }
}
