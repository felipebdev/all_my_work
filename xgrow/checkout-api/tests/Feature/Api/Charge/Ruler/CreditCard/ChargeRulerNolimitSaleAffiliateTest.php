<?php

namespace Tests\Feature\Api\Charge\Ruler\CreditCard;

use App\Payment;
use App\PaymentPlanSplit;
use App\Producer;
use App\ProducerProduct;
use App\Services\Actions\RunChargeRulerForNoLimitAction;
use App\Services\Actions\RunRegularChargeForNoLimitAction;
use App\Services\Charges\NoLimitChargeService;
use App\Subscription;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\Feature\Helper\MundipaggToken;
use Tests\Feature\Traits\CreateSubscriberTrait;
use Tests\Feature\Traits\Database\ValidatesCreditCardDatabase;
use Tests\Feature\Traits\Integration\MockPubSubTrait;
use Tests\Feature\Traits\Json\ValidatesCreditCardJsonPayload;
use Tests\Feature\Traits\LocalDatabaseIds;
use Tests\TestCase;

class ChargeRulerNolimitSaleAffiliateTest extends TestCase
{


    use MockPubSubTrait;
    use WithFaker;
    use CreateSubscriberTrait;
    use LocalDatabaseIds;
    use ValidatesCreditCardJsonPayload;
    use ValidatesCreditCardDatabase;

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

    public function test_no_limit_sale_with_coupon_with_order_bump_with_affiliate()
    {
        $producer = Producer::where('type', 'A')->first();
        $producer->producerProduct->each(fn(ProducerProduct $producerProduct) => $producerProduct->update([
            'status' => 'active',
        ]));

        $this->withoutMiddleware();

        $this->mockPubSubWithCount(1);

        $subscriberId = $this->createSubscriber($this->platformId, $this->salePlanId);

        $token = MundipaggToken::insufficientBalance($this->faker->creditCardNumber('MasterCard'));

        $response = $this->postJson("/api/checkout/{$this->platformId}/{$this->salePlanId}", [
            "payment_method" => "credit_card",
            "cc_info" => [
                [
                    "token" => "$token",
                    "installment" => 12,
                    "value" => "150"
                ]
            ],
            "subscriber_id" => $subscriberId,
            "platform_id" => $this->platformId,
            "plan_id" => $this->salePlanId,
            "order_bump" => $this->orderBumps,
            "affiliate_id" => '2'
        ]);

        if ($response->getStatusCode() == 409) {
            $this->markTestSkipped('Gateway problem');
        }

        $response->assertStatus(200);

        $this->assertJsonPayload($response);

        $orderCode = $response->json('*.order_code')[0] ?? null;

        $payment = Payment::where('order_code', $orderCode)->first();
        $orderNumber = $payment->order_number;

        $this->assertDatabaseCreditCard($subscriberId, $this->salePlanId, $orderCode, 15); // 12x15

        $this->assertEquals(5,  PaymentPlanSplit::where('order_code', $orderCode)->count()); // X, C, A + X, C

        // renew on due date
        $dueDate = Carbon::now()->addDays(30); // next recurrence: D
        Carbon::setTestNow($dueDate);

        NoLimitChargeService::$forceFailStatusDebug = true;

        $action = new RunRegularChargeForNoLimitAction(['subscriber_id' => $subscriberId]);
        $action();

        // Main Product
        $this->assertDatabaseHas('subscriptions', [
            'subscriber_id' => $subscriberId,
            'plan_id' => $this->salePlanId,
            'status' => Subscription::STATUS_FAILED,
        ]);

        // Order bump
        $this->assertDatabaseHas('subscriptions', [
            'subscriber_id' => $subscriberId,
            'plan_id' => $this->orderBumps[0],
            'status' => Subscription::STATUS_FAILED,
        ]);

        // 1st retry (must success)

        NoLimitChargeService::$forceFailStatusDebug = false;

        Carbon::setTestNow($dueDate->clone()->addDays(5)); // D+5

        $action5d = new RunChargeRulerForNoLimitAction(['subscriber_id' => $subscriberId]);
        $action5d();

        // assert that renew follows same configuration of the original order
        $orderCode2 = DB::table('payments')
                ->whereNotNull('order_code')
                ->latest('id')
                ->first()
                ->order_code ?? null;

        $this->assertDatabaseHas('payments', [
            'order_code' => $orderCode2,
            'installment_number' => 2,
            'installments' => 12,
            'status' => Payment::STATUS_PAID,
            'plans_value' => 12.5, // original value
            'price' => 15.0, // with interest
        ]);

        $lastPaidPayment = Payment::where('status', 'paid')->latest('id')->first();
        $this->assertNotEquals($lastPaidPayment->payment_date, $lastPaidPayment->confirmed_at->format('Y-m-d'));

        $this->assertDatabaseHas('subscriptions', [
            'subscriber_id' => $subscriberId,
            'plan_id' => $this->salePlanId,
            'status' => Subscription::STATUS_ACTIVE,
        ]);

        $this->assertDatabaseHas('subscriptions', [
            'subscriber_id' => $subscriberId,
            'plan_id' => $this->orderBumps[0],
            'status' => Subscription::STATUS_ACTIVE,
        ]);

        $paymentPlanSplitOrderCode = DB::table('payment_plan_split')
                ->whereNotNull('order_code')
                ->latest('id')
                ->first()
                ->order_code ?? null;

        // X, C, A + X, C
        $this->assertEquals(5, PaymentPlanSplit::where('order_code', $paymentPlanSplitOrderCode)->count());
    }
}
