<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\User::class)->create([
            'name'      => 'Admin User',
            'email'     => 'admin@admin.com',
            'password'  => Hash::make('admin'),
        ]);

        factory(\App\User::class)->create([
            'name'      => 'John Doe',
            'email'     => 'john.doe@admin.com',
            'password'  => Hash::make('admin'),
        ]);

        factory(\App\User::class)->create([
            'name'      => 'Jane Smith',
            'email'     => 'jane.smith@admin.com',
            'password'  => Hash::make('admin'),
        ]);
    }
}
