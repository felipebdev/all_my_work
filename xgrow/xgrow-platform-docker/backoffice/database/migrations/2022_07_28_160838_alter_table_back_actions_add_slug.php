<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AlterTableBackActionsAddSlug extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('back_actions', function (Blueprint $table) {
            $table->string('slug')->nullable();
        });
        
        DB::table('back_actions')->where(['id' => 1])->update(['slug' => 'store']);
        DB::table('back_actions')->where(['id' => 2])->update(['slug' => 'update']);
        DB::table('back_actions')->where(['id' => 3])->update(['slug' => 'destroy']);
        DB::table('back_actions')->where(['id' => 4])->update(['slug' => 'export']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('back_actions', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
}
