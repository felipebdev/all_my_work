<?php

namespace Tests\Feature\Api\Charge\Regular\Subscription;

use App\Payment;
use App\Services\Actions\RunRegularChargeForSubscriptionAction;
use App\Subscriber;
use App\Subscription;
use Carbon\Carbon;
use Tests\Feature\Helper\MundipaggToken;
use Tests\Feature\Traits\CreateSubscriberTrait;
use Tests\Feature\Traits\Integration\MockPubSubTrait;
use Tests\Feature\Traits\LocalDatabaseIds;
use Tests\TestCase;

class ChargeSubscriptionCreditCardWithInstallmentTest extends TestCase
{

    use MockPubSubTrait;
    use CreateSubscriberTrait;
    use LocalDatabaseIds;
    public function test_regular_charge_subscription_with_installments_using_credit_card_success()
    {
        $this->withoutMiddleware();

        $this->mockPubSubWithCount(1);

        $subscriberId = $this->createSubscriber($this->platformId, $this->subscriptionPlanId);

        $response = $this->postJson("/api/checkout/{$this->platformId}/{$this->subscriptionPlanId}", [
            "payment_method" => "credit_card",
            "cc_info" => [
                [
                    "token" => MundipaggToken::cardOk($this->faker->creditCardNumber('MasterCard')),
                    "installment" => 12,
                    "value" => "100.00"
                ]
            ],
            "subscriber_id" => $subscriberId,
            "platform_id" => $this->platformId,
            "plan_id" => $this->subscriptionPlanId,
            "order_bump" => []
        ]);

        if ($response->getStatusCode() == 409) {
            $this->markTestSkipped('Gateway problem');
        }

        $response->assertStatus(200);

        $orderCode = $response->json('*.order_code')[0] ?? null;

        $this->assertDatabaseHas('subscribers', [
            'id' => $subscriberId,
            'status' => Subscriber::STATUS_ACTIVE,
        ]);

        $this->assertDatabaseHas('subscriptions', [
            'subscriber_id' => $subscriberId,
            'plan_id' => $this->subscriptionPlanId,
            'status' => Subscription::STATUS_ACTIVE,
        ]);

        $this->assertDatabaseHas('payments', [
            'order_code' => $orderCode,
            'status' => Payment::STATUS_PAID,
            'plans_value' => 100, // original value
            'price' => 120, // with interest
        ]);

        ///////// 1st "renew"

        $dueDate = Carbon::now()->addDays(30); // next recurrence: D
        Carbon::setTestNow($dueDate);

        $action = new RunRegularChargeForSubscriptionAction(['subscriber_id' => $subscriberId]);
        $action();

        $this->assertDatabaseHas('subscriptions', [
            'subscriber_id' => $subscriberId,
            'plan_id' => $this->subscriptionPlanId,
            'status' => Subscription::STATUS_ACTIVE,
        ]);

        // assert that renew follows same configuration of the original order

        $this->assertDatabaseHas('payments', [
            //'order_code' => $orderCode,
            'installment_number' => 2,
            'installments' => 12,
            'status' => Payment::STATUS_PAID,
            'plans_value' => 100, // original value
            'price' => 120, // with interest
        ]);

        $lastPaidPayment = Payment::where('status', 'paid')->latest('id')->first();
        $this->assertEquals($lastPaidPayment->payment_date, $lastPaidPayment->confirmed_at->format('Y-m-d'));
    }
}
