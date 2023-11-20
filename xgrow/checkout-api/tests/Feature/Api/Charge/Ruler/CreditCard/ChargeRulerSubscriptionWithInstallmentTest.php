<?php

namespace Tests\Feature\Api\Charge\Ruler\CreditCard;

use App\Payment;
use App\Services\Actions\RunChargeRulerForSubscriptionsAction;
use App\Services\Actions\RunRegularChargeForSubscriptionAction;
use App\Services\Mundipagg\CreditCardRecurrenceService;
use App\Subscription;
use Carbon\Carbon;
use Tests\Feature\Helper\MundipaggToken;
use Tests\Feature\Traits\CreateSubscriberTrait;
use Tests\Feature\Traits\Integration\MockPubSubTrait;
use Tests\Feature\Traits\LocalDatabaseIds;
use Tests\TestCase;

class ChargeRulerSubscriptionWithInstallmentTest extends TestCase
{

    use MockPubSubTrait;
    use CreateSubscriberTrait;
    use LocalDatabaseIds;

    public function test_all_retries_fail()
    {
        $this->withoutMiddleware();

        $this->mockPubSubWithCount(2);

        $subscriberId = $this->createSubscriber($this->platformId, $this->subscriptionPlanId);

        $token = MundipaggToken::cardOk($this->faker->creditCardNumber('MasterCard'));

        $response = $this->postJson("/api/checkout/{$this->platformId}/{$this->subscriptionPlanId}", [
            "payment_method" => "credit_card",
            "subscriber_id" => $subscriberId,
            "platform_id" => $this->platformId,
            "plan_id" => $this->subscriptionPlanId, // 100
            "order_bump" => [],
            "cc_info" => [
                [
                    "token" => "$token",
                    "installment" => 12,
                    "value" => "100.00"
                ]
            ],
        ]);

        if ($response->getStatusCode() == 409) {
            $this->markTestSkipped('Gateway problem');
        }

        $response->assertStatus(200);

        $dueDate = Carbon::now()->addDays(30); // next recurrence: D

        // renew on due date

        Carbon::setTestNow($dueDate);

        CreditCardRecurrenceService::$forceFailStatusDebug = true;

        $regularCharge = new RunRegularChargeForSubscriptionAction(['subscriber_id' => $subscriberId]);
        $regularCharge();

        // 1st retry

        Carbon::setTestNow($dueDate->clone()->addDays(5)); // D+5

        /** @var RunChargeRulerForSubscriptionsAction $action5d */
        $action5d = new RunChargeRulerForSubscriptionsAction(['subscriber_id' => $subscriberId]);
        $action5d();

        // 2nd retry

        Carbon::setTestNow($dueDate->clone()->addDays(12)); // D+12

        $action12d = new RunChargeRulerForSubscriptionsAction(['subscriber_id' => $subscriberId]);
        $action12d();

        // 3rd (and last) retry

        Carbon::setTestNow($dueDate->clone()->addDays(20)); // D+20

        $action20d = new RunChargeRulerForSubscriptionsAction(['subscriber_id' => $subscriberId]);
        $action20d();

        $this->assertDatabaseHas('subscriptions', [
            'subscriber_id' => $subscriberId,
            'plan_id' => $this->subscriptionPlanId,
            'status' => Subscription::STATUS_CANCELED, // subscription is canceled
        ]);

        $lastPayment = Payment::latest('id')->first();
        $this->assertNull($lastPayment->confirmed_at);
    }

    public function test_success_on_second_retry()
    {
        $this->withoutMiddleware();

        $this->mockPubSubWithCount(1);

        $subscriberId = $this->createSubscriber($this->platformId, $this->subscriptionPlanId);

        $token = MundipaggToken::cardOk($this->faker->creditCardNumber('MasterCard'));

        $response = $this->postJson("/api/checkout/{$this->platformId}/{$this->subscriptionPlanId}", [
            "payment_method" => "credit_card",
            "cc_info" => [
                [
                    "token" => "$token",
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

        $dueDate = Carbon::now()->addDays(30); // next recurrence: D

        $orderCode = $response->json('*.order_code')[0] ?? null;

        $payment = Payment::where('order_code', $orderCode)->first();
        $orderNumber = $payment->order_number;

        // renew on due date

        Carbon::setTestNow($dueDate);

        CreditCardRecurrenceService::$forceFailStatusDebug = true;

        $regularCharge = new RunRegularChargeForSubscriptionAction(['subscriber_id' => $subscriberId]);
        $regularCharge();

        // 1st retry

        Carbon::setTestNow($dueDate->clone()->addDays(5)); // D+5

        /** @var RunChargeRulerForSubscriptionsAction $action5d */
        $action5d = $this->app->make(RunChargeRulerForSubscriptionsAction::class);
        $action5d();

        // assert subscription status
        $this->assertDatabaseHas('subscriptions', [
            'subscriber_id' => $subscriberId,
            'plan_id' => $this->subscriptionPlanId,
            'status' => Subscription::STATUS_FAILED,
        ]);

        // 2nd retry

        CreditCardRecurrenceService::$forceFailStatusDebug = false;

        Carbon::setTestNow($dueDate->clone()->addDays(12)); // D+12

        /** @var RunChargeRulerForSubscriptionsAction $action12d */
        $action12d = $this->app->make(RunChargeRulerForSubscriptionsAction::class);
        $action12d();

        // assert recurrence updated
        $this->assertDatabaseHas('recurrences', [
            'order_number' => $orderNumber,
            'current_charge' => 2,
            'default_installments' => 12,
        ]);

        // assert payment created with correct values
        $this->assertDatabaseHas('payments', [
            'order_number' => $orderNumber,
            'installment_number' => 2,
            'installments' => 12,
            'status' => Payment::STATUS_PAID,
            'plans_value' => 100, // original value
            'price' => 120, // with interest
        ]);

        $lastPaidPayment = Payment::where('status', 'paid')->latest('id')->first();
        $this->assertNotEquals($lastPaidPayment->payment_date, $lastPaidPayment->confirmed_at->format('Y-m-d'));

        $this->assertDatabaseHas('subscriptions', [
            'subscriber_id' => $subscriberId,
            'plan_id' => $this->subscriptionPlanId,
            'status' => Subscription::STATUS_ACTIVE,
        ]);
    }
}
