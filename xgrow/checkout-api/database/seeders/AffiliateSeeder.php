<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\Feature\Traits\LocalDatabaseIds;

class AffiliateSeeder extends Seeder
{
    use LocalDatabaseIds;

    public function run()
    {
        $platformUserId = DB::table('platforms_users')->insertGetId([
            'name' => 'Afiliado',
            'email' => 'afiliado@xgrow.com',
            'password' => Hash::make('password'),
            'active' => 1,
            'accepted_terms' => 1,
        ]);

        $affiliateId = DB::table('producers')->insertGetId([
            'platform_id' => '00000000-0000-0000-0000-000000000000',
            'platform_user_id' => $platformUserId,
            'type' => 'A',
            'accepted_terms' => 1,
            'document_type' => 'cpf',
            'document' => '22222222222',
            'holder_name' => 'AFILIADO',
            'account_type' => 'savings',
            'bank' => '001',
            'branch' => '0123',
            'account' => '2345',
            'branch_check_digit' => '1',
            'account_check_digit' => '1',
            'document_verified' => 0,
            'recipient_id' => 'rp_k0qmoE5T1TJo4RV7',
            'recipient_gateway' => 'mundipagg',
        ]);

        // Sale "contract"
        DB::table('producer_products')->insert([
            'producer_id' => $affiliateId,
            'product_id' => $this->salePlanId,
            'contract_limit' => null,
            'percent' => 10.0,
            'status' => 'pending', // add as pending by default
            'canceled_at' => null,
            'split_invoice' => 0,
        ]);

        // Subscription "contract"
        DB::table('producer_products')->insert([
            'producer_id' => $affiliateId,
            'product_id' => $this->subscriptionPlanId,
            'contract_limit' => null,
            'percent' => 10.0,
            'status' => 'pending', // add as pending by default
            'canceled_at' => null,
            'split_invoice' => 0,
        ]);

        // Upsell "contract"
        DB::table('producer_products')->insert([
            'producer_id' => $affiliateId,
            'product_id' => $this->upsell,
            'contract_limit' => null,
            'percent' => 10.0,
            'status' => 'pending', // add as pending by default
            'canceled_at' => null,
            'split_invoice' => 0,
        ]);
    }
}
