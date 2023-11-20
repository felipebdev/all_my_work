<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableCampaignsAddColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('campaigns', function (Blueprint $table) {
            // Temporarily set status as nullable
            $table->string('status')->nullable()->after('type');
            $table->unsignedInteger('sent')->default(0)->after('replyto');
        });

        // Set pre-existent scheduled campaign rows
        \DB::statement("UPDATE campaigns SET status = 'pending' WHERE status IS NULL AND type = 1");

        // Set pre-existent automatic campaign rows
        \DB::statement("UPDATE campaigns SET status = 'started' WHERE status IS NULL AND type = 2");

        Schema::table('campaigns', function (Blueprint $table) {
            // Set status as NOT nullable
            $table->string('status')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('sent');
        });
    }
}
