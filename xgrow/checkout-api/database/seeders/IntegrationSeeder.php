<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Modules\Integration\Enums\CodeEnum;
use Modules\Integration\Enums\TypeEnum;
use Tests\Feature\Traits\LocalDatabaseIds;

class IntegrationSeeder extends Seeder
{
    use LocalDatabaseIds;


    public function run()
    {
        $integrationId = DB::table('apps')->insertGetId([
            'platform_id' => $this->platformId,
            'is_active' => 1,
            'description' => 'Webhook Integration',
            'code' => CodeEnum::WEBHOOK,
            'type' => TypeEnum::WEBHOOK,
            'api_key' => Crypt::encrypt('0000000'),
            'api_webhook' => Crypt::encrypt('https://example.com')
        ]);

        $metadata = [
            "days_never_accessed" => null
        ];

        $events = [
            'onCreateLead',
            'onAbandonedCart',
            'onCreateBankSlip',
            'onCreatePix',
            'onApprovePayment',
            'onRefusePayment',
            'onRefundPayment',
            'onChargebackPayment',
            'onCancelSubscription',
        ];

        foreach ($events as $event) {
            $actionsId = DB::table('app_actions')->insertGetId([
                "app_id" => $integrationId,
                "platform_id" => $this->platformId,
                "is_active" => 1,
                "description" => "Test {$event}",
                "event" => "{$event}",
                "action" => "bindTriggerWebhook",
                "metadata" => Crypt::encrypt($metadata),
            ]);

            DB::table('app_action_products')->insertGetId([
                "app_action_id" => $actionsId,
                "product_id" => 1,
                "plan_id" => $this->salePlanId,
            ]);
        }
    }
}
