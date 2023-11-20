<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\Feature\Traits\LocalDatabaseIds;

class ProducerSeeder extends Seeder
{

    use LocalDatabaseIds;

    public function run()
    {
        $faker = \Faker\Factory::create();
        $faker->addProvider(new \Faker\Provider\pt_BR\Person($faker));

        $platformUserId = DB::table('platforms_users')->insertGetId([
            'name' => 'Coprodutor',
            'email' => 'coprodutor@xgrow.com',
            'password' => Hash::make('password'),
            'active' => 1,
            'accepted_terms' => 1,
        ]);

        $producerId = DB::table('producers')->insertGetId([
            'platform_id' => $this->platformId,
            'platform_user_id' => $platformUserId,
            'accepted_terms' => 1,
            'document_type' => null,
            'document' => '012.345.678-90',
            'holder_name' => 'COPRODUTOR',
            'account_type' => 'savings',
            'bank' => '001',
            'branch' => '0123',
            'account' => '2345',
            'branch_check_digit' => '1',
            'account_check_digit' => '1',
            'document_verified' => 0,
            'recipient_id' => 'rp_v45N8DSkjh7KNemo',
            'recipient_gateway' => 'mundipagg',
        ]);

        $producerProductId = DB::table('producer_products')->insertGetId([
            'producer_id' => $producerId,
            'product_id' => $this->salePlanId,
            'contract_limit' => null,
            'percent' => 20.0,
            'status' => 'pending', // add as pending by default
            'canceled_at' => null,
            'split_invoice' => 0,
        ]);
    }
}
