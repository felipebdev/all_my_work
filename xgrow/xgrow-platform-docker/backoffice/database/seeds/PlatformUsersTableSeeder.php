<?php

use Illuminate\Database\Seeder;

class PlatformUsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('platforms_users')->insert([
            'name' => 'Platform User',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin'),
            'platform_id' => '97cc8320-8b84-47de-b32b-e8cc47f79401'
        ]);
    }
}
