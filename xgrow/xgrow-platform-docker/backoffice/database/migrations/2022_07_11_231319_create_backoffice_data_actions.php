<?php

use App\BackAction;
use Illuminate\Database\Migrations\Migration;

class CreateBackofficeDataActions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        BackAction::create(['name' => 'store']);
        BackAction::create(['name' => 'update']);
        BackAction::create(['name' => 'destroy']);
        BackAction::create(['name' => 'export']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        BackAction::truncate();
    }
}
