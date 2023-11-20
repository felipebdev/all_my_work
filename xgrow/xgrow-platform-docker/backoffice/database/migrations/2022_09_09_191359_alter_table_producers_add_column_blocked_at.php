<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AlterTableProducersAddColumnBlockedAt extends Migration
{

    public function up()
    {
        Schema::table('producers', function (Blueprint $table) {
            $table->timestamp('blocked_at')->nullable()->after('updated_at');
        });

        DB::statement("ALTER TABLE producer_products CHANGE COLUMN status status ENUM('active','canceled','pending','blocked') NOT NULL DEFAULT 'pending'");
    }

    public function down()
    {
        Schema::table('producers', function (Blueprint $table) {
            $table->dropColumn('blocked_at');
        });

        DB::statement("ALTER TABLE producer_products CHANGE COLUMN status status ENUM('active','canceled','pending') NOT NULL DEFAULT 'pending'");
    }
}
