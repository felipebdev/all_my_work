<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SeedTablePlatformUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $platformUsers = DB::table('platforms_users')->get();
        try {
            DB::beginTransaction();
                foreach ($platformUsers as $user) {
                    DB::table('platform_user')->insert([
                        'platform_id' => $user->platform_id,
                        'platforms_users_id' => $user->id
                    ]);
                }
            DB::commit();
        }
        catch(Exception $e) {
            DB::rollBack();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('platform_user')->truncate();
    }
}
