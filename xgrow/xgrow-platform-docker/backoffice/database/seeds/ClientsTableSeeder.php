<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Client;

class ClientsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Client::factory()
                    ->count(5)
                    ->create();
    }
}
