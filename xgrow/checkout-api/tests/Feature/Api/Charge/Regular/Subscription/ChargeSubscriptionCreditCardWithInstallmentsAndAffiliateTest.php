<?php

namespace Tests\Feature\Api\Charge\Regular\Subscription;

use App\Payment;
use App\PaymentPlanSplit;
use App\Producer;
use App\ProducerProduct;
use App\Services\Actions\RunRegularChargeForSubscriptionAction;
use App\Subscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\Feature\Helper\MundipaggToken;
use Tests\Feature\Traits\CreateSubscriberTrait;
use Tests\Feature\Traits\Integration\MockPubSubTrait;
use Tests\Feature\Traits\LocalDatabaseIds;
use Tests\TestCase;

/**
 * Test Subscription Sale via Credit Card using installments
 */
class ChargeSubscriptionCreditCardWithInstallmentsAndAffiliateTest extends TestCase
{

    use MockPubSubTrait;
    use CreateSubscriberTrait;
    use LocalDatabaseIds;

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

    public function test_regular_charge_subscription_without_coupon_without_order_bump_using_credit_card_success()
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

        $this->assertEquals(3, PaymentPlanSplit::where('order_code', $orderCode)->count()); // X, C, A

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
        $this->assertEquals($lastPaidPayment->payment_date, $lastPaidPayment->confirmed_at->format('Y-m-d'));

        $this->assertEquals(3, PaymentPlanSplit::where('order_code', $orderCode2)->count()); // X, C, A
    }
}
