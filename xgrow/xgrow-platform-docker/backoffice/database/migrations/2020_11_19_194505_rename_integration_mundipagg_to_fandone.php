<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class RenameIntegrationMundipaggToFandone extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('integrations')
            ->where('id_integration', 'MUNDIPAGG')
            ->update(['id_integration' => 'FANDONE']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('integrations')
            ->where('id_integration', 'FANDONE')
            ->update(['id_integration' => 'MUNDIPAGG']);
    }
}
