<?php

namespace Tests\Feature\Api\Charge\Regular\Subscription;

use App\Http\Controllers\Webhooks\StatusPagarmeController;
use App\Payment;
use App\Services\Actions\RunRegularChargeForSubscriptionAction;
use Carbon\Carbon;
use Tests\Feature\Traits\CreateSubscriberTrait;
use Tests\Feature\Traits\LocalDatabaseIds;
use Tests\TestCase;

class ChargeSubscriptionPixTest extends TestCase
{

    use CreateSubscriberTrait;
    use LocalDatabaseIds;

    public function test_regular_charge_subscription_using_pix_success()
    {
        $this->markTestIncomplete('Regular charge of PIX changed, fix this test');

        $this->withoutMiddleware();

        $subscriberId = $this->createSubscriber($this->platformId, $this->subscriptionPlanId);

        $response = $this->postJson("/api/checkout/{$this->platformId}/{$this->subscriptionPlanId}", [
            'payment_method' => 'pix',
            'subscriber_id' => $subscriberId,
            'platform_id' => $this->platformId,
            'plan_id' => $this->subscriptionPlanId,
            'order_bump' => []
        ]);

        if ($response->getStatusCode() == 409) {
            $this->markTestSkipped('Gateway problem');
        }

        $response->assertStatus(200);

        $orderCodes = $response->json('*.order_code');

        /////// fake postback

        StatusPagarmeController::$skipPostbackSignatureValidation = true;

        $response = $this->withHeaders(['X-Hub-Signature' => '123'])
            ->json('POST', '/api/checkout/order/paid/pagarme', [
                'id' => '523247310',
                'object' => 'transaction',
                'transaction' => [
                    'object' => 'transaction',
                    'status' => 'paid',
                    'tid' => $orderCodes[0],
                    'payment_method' => 'pix',
                    'metadata' => [
                        'antecipation_value' => '0',
                        'customer_value' => '93.5',
                        'plans_value' => '100',
                        'service_value' => '95.00',
                        'tax_value' => '5',
                    ],
                ],
            ]);

        /////// "renew"

        $dueDate = Carbon::now()->addDays(30)->subDays(5);

        //dump('send PIX 5 days before');

        Carbon::setTestNow($dueDate);

        $action = new RunRegularChargeForSubscriptionAction(['subscriber_id' => $subscriberId]);
        $action();

        $this->assertDatabaseHas('payments', [
            'subscriber_id' => $subscriberId,
            'payment_source' => Payment::PAYMENT_SOURCE_AUTOMATIC,
            'type_payment' => Payment::TYPE_PAYMENT_PIX,
            'status' => Payment::STATUS_PENDING,
        ]);

    }
}
