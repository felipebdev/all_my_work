<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Platform;

class PlatformsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Platform::factory()
                    ->count(5)
                    ->create();
    }
}
