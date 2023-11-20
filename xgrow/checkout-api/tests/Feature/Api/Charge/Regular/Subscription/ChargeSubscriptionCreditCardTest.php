<?php

namespace Tests\Feature\Api\Charge\Regular\Subscription;

use App\Mail\SendMailRecurrencePaymentSuccess;
use App\Payment;
use App\Plan;
use App\Services\Actions\RunRegularChargeForSubscriptionAction;
use App\Subscriber;
use App\Subscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Tests\Feature\Helper\MundipaggToken;
use Tests\Feature\Traits\CreateSubscriberTrait;
use Tests\Feature\Traits\Integration\MockPubSubTrait;
use Tests\Feature\Traits\LocalDatabaseIds;
use Tests\TestCase;

class ChargeSubscriptionCreditCardTest extends TestCase
{

    use MockPubSubTrait;
    use CreateSubscriberTrait;
    use LocalDatabaseIds;

    public function test_regular_subscription_charge_using_credit_card_success()
    {
        //Queue::fake();

        Plan::where('id', $this->subscriptionPlanId)->update(['price' => '100']);

        $this->withoutMiddleware();

        $this->mockPubSubWithCount(1);

        $subscriberId = $this->createSubscriber($this->platformId, $this->subscriptionPlanId);

        $token = MundipaggToken::cardOk($this->faker->creditCardNumber('MasterCard'));

        $response = $this->postJson("/api/checkout/{$this->platformId}/{$this->subscriptionPlanId}", [
            "payment_method" => "credit_card",
            "cc_info" => [
                [
                    "token" => "$token",
                    "installment" => 1,
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

        $this->assertDatabaseHas('subscribers', [
            'id' => $subscriberId,
            'status' => Subscriber::STATUS_ACTIVE,
        ]);

        $this->assertDatabaseHas('subscriptions', [
            'subscriber_id' => $subscriberId,
            'plan_id' => $this->subscriptionPlanId,
            'status' => Subscription::STATUS_ACTIVE,
        ]);

        //Plan::where('id', $this->subscriptionPlanId)->update(['price' => '200']);

        ///////// 1st "renew"

        $dueDate = Carbon::now()->addDays(30); // next recurrence: D

        //dump('renew on due date: must success');

        Carbon::setTestNow($dueDate);

        $action = new RunRegularChargeForSubscriptionAction(['subscriber_id' => $subscriberId]);
        $action();

        $this->assertDatabaseHas('subscriptions', [
            'subscriber_id' => $subscriberId,
            'plan_id' => $this->subscriptionPlanId,
            'status' => Subscription::STATUS_ACTIVE,
        ]);

        //// 2nd "renew"

        Mail::fake(); // disable real sending email, enable fake

        $dueDate = Carbon::now()->addDays(30); // add more 30 days

        //dump('2nd renew on due date: must success');

        Carbon::setTestNow($dueDate);

        $action = new RunRegularChargeForSubscriptionAction(['subscriber_id' => $subscriberId]);
        $action();

        $this->assertDatabaseHas('subscriptions', [
            'subscriber_id' => $subscriberId,
            'plan_id' => $this->subscriptionPlanId,
            'status' => Subscription::STATUS_ACTIVE,
        ]);

        Mail::assertSent(SendMailRecurrencePaymentSuccess::class);

        $lastPaidPayment = Payment::where('status', 'paid')->latest('id')->first();
        $this->assertEquals($lastPaidPayment->payment_date, $lastPaidPayment->confirmed_at->format('Y-m-d'));
    }
}
