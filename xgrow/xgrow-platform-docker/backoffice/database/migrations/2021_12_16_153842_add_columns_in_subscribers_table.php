<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsInSubscribersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscribers', function (Blueprint $table) {
            $table->dateTime('email_bounce_at')->nullable()->after('email');
            $table->string('email_bounce_id')->nullable()->after('email_bounce_at');
            $table->string('email_bounce_type')->nullable()->after('email_bounce_id');
            $table->string('email_bounce_description')->nullable()->after('email_bounce_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subscribers', function (Blueprint $table) {
            $table->dropColumn('email_bounce_at');
            $table->dropColumn('email_bounce_id');
            $table->dropColumn('email_bounce_type');
            $table->dropColumn('email_bounce_description');
        });
    }
}
