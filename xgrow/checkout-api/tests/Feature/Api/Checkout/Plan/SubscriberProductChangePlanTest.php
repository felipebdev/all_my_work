<?php

namespace Tests\Feature\Api\Checkout\Plan;

use App\Payment;
use App\Plan;
use App\Services\Actions\RunRegularChargeForSubscriptionAction;
use App\Subscriber;
use App\Subscription;
use Carbon\Carbon;
use Exception;
use Illuminate\Testing\TestResponse;
use Tests\Feature\Helper\MundipaggToken;
use Tests\Feature\Traits\CreateSubscriberTrait;
use Tests\Feature\Traits\LocalDatabaseIds;
use Tests\TestCase;

class SubscriberProductChangePlanTest extends TestCase
{

    use CreateSubscriberTrait;
    use LocalDatabaseIds;

    private int $affiliate_id = 1;

    protected function setUp(): void
    {
        parent::setUp();

        // enable change on all products
        Plan::query()->update([
            'allow_change' => true,
        ]);
    }

    protected function tearDown(): void
    {
        // disable change on all products
        Plan::query()->update([
            'allow_change' => false,
        ]);

        parent::tearDown();
    }

    /**
     * Validation task XXP-70
     * @return void
     * @throws Exception
     */
    public function test_subscriber_product_change_plan_test()
    {

        $product_id = 2;

        $this->withoutMiddleware();
        $subscriberId = $this->createSubscriber($this->platformId, $this->subscriptionPlanId);
        $response = $this->newSubscription($subscriberId, $this->subscriptionPlanId);
        if ($response->getStatusCode() == 409) {
            $this->markTestSkipped('Gateway problem');
        }

        $response->assertStatus(200);
        $this->assertSubscriber($subscriberId);
        $this->assertSubscription($subscriberId, $this->subscriptionPlanId);

        $subscription = Subscription::where(
            ['subscriber_id' => $subscriberId,
            'plan_id' => $this->subscriptionPlanId]
        )->first();

        //same product plan Mensal100tres80
        $newPlanId = 5;

        //plan change
        $response = $this->postJson("/api/students/products/{$product_id}/change", [
            "subscription_id" => $subscription->id,
            "new_plan_id" => $newPlanId
        ]);
        if ($response->getStatusCode() == 409) {
            $this->markTestSkipped('Gateway problem');
        }

        $response->assertStatus(200);

        //check plan change by another recorrency period (XXP-70 test)
        $this->assertSubscription($subscriberId, $newPlanId);

        //checks if it keeps the same affiliate (XXP-70 test)
        $this->assertRecurrence($subscriberId, $newPlanId);

        ///////// 1st "renew"
        $dueDate = Carbon::now()->addDays(30); // next recurrence: D
        Carbon::setTestNow($dueDate);

        $this->runRegularChargeForSubscriptionAction($subscriberId);

        //Renewal of installments always at the cash value (XXP-70 test)
        $this->assertPayment($subscriberId, $newPlanId, 'installments', 1);

        //checks if discounts for the first N “installments” are applied (XXP-70 test)
        $this->assertPayment($subscriberId, $newPlanId, 'plans_value', 80.00);

        //check if discount is applied and has no interests
        $this->assertPayment($subscriberId, $newPlanId, 'price', 80.00);

    }

    /**
     * @param int $subscriberId
     * @param int $planId
     * @return TestResponse
     */
    private function newSubscription(int $subscriberId, int $planId): TestResponse
    {
        $token = MundipaggToken::cardOk($this->faker->creditCardNumber('MasterCard'));

        return $this->postJson("/api/checkout/{$this->platformId}/{$planId}", [
            "payment_method" => "credit_card",
            "affiliate_id" => $this->affiliate_id,
            "cc_info" => [
                [
                    "token" => "$token",
                    "installment" => 12,
                    "value" => "100.00" // Full price on checkout
                ]
            ],
            "subscriber_id" => $subscriberId,
            "platform_id" => $this->platformId,
            "plan_id" => $planId
        ]);
    }

    private function assertSubscription(int $subscriberId, int $planId)
    {
        $this->assertDatabaseHas('subscriptions', [
            'subscriber_id' => $subscriberId,
            'plan_id' => $planId,
            'status' => Subscription::STATUS_ACTIVE,
        ]);
    }

    private function assertSubscriber(int $subscriberId)
    {
        $this->assertDatabaseHas('subscribers', [
            'id' => $subscriberId,
            'status' => Subscriber::STATUS_ACTIVE,
        ]);
    }

    private function assertRecurrence(int $subscriberId, int $planId)
    {
        $this->assertDatabaseHas('recurrences', [
            'subscriber_id' => $subscriberId,
            'plan_id' => $planId,
            'affiliate_id' => $this->affiliate_id,
        ]);
    }

    private function runRegularChargeForSubscriptionAction(int $subscriberId)
    {
        (new RunRegularChargeForSubscriptionAction(['subscriber_id' => $subscriberId]))();
    }

    private function assertPayment(int $subscriber_id, int $plan_id, string $column, $value)
    {
        $payment = Payment::select("payments.{$column}")
            ->join('payment_plan', 'payment_plan.payment_id', 'payments.id')
            ->where('payments.subscriber_id', $subscriber_id)
            ->where('payment_plan.plan_id', $plan_id)
            ->first();

      $this->assertEquals($value, $payment->{$column});

    }
}
