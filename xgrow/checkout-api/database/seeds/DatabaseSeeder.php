<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
//        $this->call(UsersTableSeeder::class);
//        $this->call(PaymentTableSeeder::class);

        dump("Creating initial data");
        DB::unprepared(file_get_contents(database_path('seeds/db-base.sql')));

        dump("Creating users and platform settings: admin@xgrow.com, cliente@xgrow.com and colaborador@xgrow.com");
        DB::unprepared(file_get_contents(database_path('seeds/db-settings.sql')));

        dump('Creating initial products and plans');
        DB::unprepared(file_get_contents(database_path('seeds/db-product-plan.sql')));

        dump('Creating coprodutor@xgrow.com');
        $this->call(\Database\Seeders\ProducerSeeder::class);

        dump('Creating affiliation settings');
        $this->call(\Database\Seeders\AffiliationSettingsSeeder::class);

        dump('Creating afiliado@xgrow.com');
        $this->call(\Database\Seeders\AffiliateSeeder::class);

        dump('Creating webhook integration');
        $this->call(\Database\Seeders\IntegrationSeeder::class);

        $this->resetPasswords();
    }

    /**
     * Reset passwords for all users.
     */
    private function resetPasswords(): void
    {
        dump('Setting all passwords to "password"');

        DB::update('update clients set password = ?', [
            Hash::make('password'),
        ]);

        DB::update('update platforms_users set password = ?', [
            Hash::make('password'),
        ]);
    }
}
