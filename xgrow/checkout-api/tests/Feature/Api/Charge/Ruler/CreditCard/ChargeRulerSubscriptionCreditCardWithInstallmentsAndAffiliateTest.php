<?php

namespace Tests\Feature\Api\Charge\Ruler\CreditCard;

use App\Payment;
use App\PaymentPlanSplit;
use App\Producer;
use App\ProducerProduct;
use App\Services\Actions\RunChargeRulerForSubscriptionsAction;
use App\Services\Actions\RunRegularChargeForSubscriptionAction;
use App\Services\Mundipagg\CreditCardRecurrenceService;
use App\Subscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\Feature\Helper\MundipaggToken;
use Tests\Feature\Traits\CreateSubscriberTrait;
use Tests\Feature\Traits\Database\ValidatesCreditCardDatabase;
use Tests\Feature\Traits\Integration\MockPubSubTrait;
use Tests\Feature\Traits\Json\ValidatesCreditCardJsonPayload;
use Tests\Feature\Traits\LocalDatabaseIds;
use Tests\TestCase;

/**
 * Test Subscription Sale via Credit Card using installments
 */
class ChargeRulerSubscriptionCreditCardWithInstallmentsAndAffiliateTest extends TestCase
{

    use MockPubSubTrait;
    use CreateSubscriberTrait;
    use LocalDatabaseIds;
    use ValidatesCreditCardJsonPayload;
    use ValidatesCreditCardDatabase;

    //use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        ProducerProduct::query()->update(['status' => 'pending']); // disable all producers/affiliates
    }

    protected function tearDown(): void
    {
        ProducerProduct::query()->update(['status' => 'pending']); // re-disable all producers/affiliates

        parent::tearDown();
    }

    public function test_subscription_charge_ruler_success()
    {
        $this->withoutMiddleware();

        $this->mockPubSubWithCount(2);

        $producer = Producer::where('type', 'A')->first();
        $producer->producerProduct->each(fn(ProducerProduct $producerProduct) => $producerProduct->update([
            'status' => 'active',
        ]));

        $subscriberId = $this->createSubscriber($this->platformId, $this->subscriptionPlanId);

        $token = MundipaggToken::cardOk($this->faker->creditCardNumber('MasterCard'));

        $response = $this->postJson("/api/checkout/{$this->platformId}/{$this->subscriptionPlanId}", [
            "payment_method" => "credit_card",
            "subscriber_id" => $subscriberId,
            "platform_id" => $this->platformId,
            "plan_id" => $this->subscriptionPlanId, // 100
            "affiliate_id" => 2,
            "order_bump" => [], // 50 - 10%
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

        $orderCode = $response->json('*.order_code')[0] ?? null;

        $payment = Payment::where('order_code', $orderCode)->first();
        $orderNumber = $payment->order_number;

        $this->assertEquals(3, PaymentPlanSplit::where('order_code', $orderCode)->count()); // X, C, A

        // renew on due date

        $dueDate = Carbon::now()->addDays(30); // next recurrence: D
        Carbon::setTestNow($dueDate);

        CreditCardRecurrenceService::$forceFailStatusDebug = true;

        $action = new RunRegularChargeForSubscriptionAction(['subscriber_id' => $subscriberId]);
        $action();

        $this->assertDatabaseHas('subscriptions', [
            'subscriber_id' => $subscriberId,
            'plan_id' => $this->subscriptionPlanId,
            'status' => Subscription::STATUS_FAILED,
        ]);

        // 1st retry (must success)

        CreditCardRecurrenceService::$forceFailStatusDebug = false;

        Carbon::setTestNow($dueDate->clone()->addDays(5)); // D+5

        $action5d = new RunChargeRulerForSubscriptionsAction(['subscriber_id' => $subscriberId]);
        $action5d();

        // assert that renew follows same configuration of the original order
        $orderCode2 = DB::table('payments')->latest('id')->first()->order_code ?? null;

        $this->assertDatabaseHas('payments', [
            'order_code' => $orderCode2,
            'installment_number' => 2,
            'installments' => 12,
            'status' => Payment::STATUS_PAID,
            'plans_value' => 100, // original value
            'price' => 120, // with interest
            'payment_source' => 'A', // automatic
        ]);

        $lastPaidPayment = Payment::where('status', 'paid')->latest('id')->first();
        $this->assertNotEquals($lastPaidPayment->payment_date, $lastPaidPayment->confirmed_at->format('Y-m-d'));

        $this->assertDatabaseHas('subscriptions', [
            'subscriber_id' => $subscriberId,
            'plan_id' => $this->subscriptionPlanId,
            'status' => Subscription::STATUS_ACTIVE,
        ]);

        // assert recurrence updated
        $this->assertDatabaseHas('recurrences', [
            'order_number' => $orderNumber,
            'current_charge' => 2,
            'default_installments' => 12,
        ]);

        $this->assertEquals(3, PaymentPlanSplit::where('order_code', $orderCode2)->count()); // X, C, A
    }

    public function test_subscription_charge_ruler_fails()
    {
        $this->withoutMiddleware();

        $this->mockPubSubWithCount(1);

        $producer = Producer::where('type', 'A')->first();
        $producer->producerProduct->each(fn(ProducerProduct $producerProduct) => $producerProduct->update([
            'status' => 'active',
        ]));

        $subscriberId = $this->createSubscriber($this->platformId, $this->subscriptionPlanId);

        $token = MundipaggToken::cardOk($this->faker->creditCardNumber('MasterCard'));

        $response = $this->postJson("/api/checkout/{$this->platformId}/{$this->subscriptionPlanId}", [
            "payment_method" => "credit_card",
            "subscriber_id" => $subscriberId,
            "platform_id" => $this->platformId,
            "plan_id" => $this->subscriptionPlanId, // 100
            "affiliate_id" => 2,
            "order_bump" => [], // 50 - 10%
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

        $orderCode = $response->json('*.order_code')[0] ?? null;

        $payment = Payment::where('order_code', $orderCode)->first();
        $orderNumber = $payment->order_number;

        $this->assertEquals(3, PaymentPlanSplit::where('order_code', $orderCode)->count()); // X, C, A

        // renew on due date

        $dueDate = Carbon::now()->addDays(30); // next recurrence: D
        Carbon::setTestNow($dueDate);

        CreditCardRecurrenceService::$forceFailStatusDebug = true;

        $action = new RunRegularChargeForSubscriptionAction(['subscriber_id' => $subscriberId]);
        $action();

        $this->assertDatabaseHas('subscriptions', [
            'subscriber_id' => $subscriberId,
            'plan_id' => $this->subscriptionPlanId,
            'status' => Subscription::STATUS_FAILED,
        ]);

        // 1st retry

        Carbon::setTestNow($dueDate->clone()->addDays(5)); // D+5

        $action5d = new RunChargeRulerForSubscriptionsAction(['subscriber_id' => $subscriberId]);
        $action5d();

        $this->assertDatabaseHas('payments', [
            'order_number' => $orderNumber,
            'payment_date' => $dueDate->toDateString(),
            'status' => Payment::STATUS_FAILED,
        ]);

        $this->assertDatabaseHas('subscriptions', [
            'subscriber_id' => $subscriberId,
            'plan_id' => $this->subscriptionPlanId,
            'status' => Subscription::STATUS_FAILED,
        ]);

        // 2nd retry

        Carbon::setTestNow($dueDate->clone()->addDays(12)); // D+12

        $action12d = new RunChargeRulerForSubscriptionsAction(['subscriber_id' => $subscriberId]);
        $action12d();

        // 3rd (and last) retry

        Carbon::setTestNow($dueDate->clone()->addDays(20)); // D+20

        $action20d = new RunChargeRulerForSubscriptionsAction(['subscriber_id' => $subscriberId]);
        $action20d();

        $this->assertDatabaseHas('payments', [
            'order_number' => $orderNumber,
            'payment_date' => $dueDate->toDateString(),
        ]);

        $this->assertDatabaseHas('subscriptions', [
            'subscriber_id' => $subscriberId,
            'plan_id' => $this->subscriptionPlanId,
            'status' => Subscription::STATUS_CANCELED, // subscription is canceled
        ]);
    }
}
