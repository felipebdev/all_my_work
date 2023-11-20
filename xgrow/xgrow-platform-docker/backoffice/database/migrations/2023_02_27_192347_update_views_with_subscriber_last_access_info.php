<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateViewsWithSubscriberLastAccessInfo extends Migration
{
    public function up()
    {
        DB::unprepared(file_get_contents(database_path('views/recurrence_charges.sql')));
        DB::unprepared(file_get_contents(database_path('views/nolimit_charges.sql')));
    }

    public function down()
    {
    }
}
