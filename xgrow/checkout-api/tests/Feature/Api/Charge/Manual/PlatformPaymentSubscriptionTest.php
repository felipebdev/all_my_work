<?php

namespace Tests\Feature\Api\Charge\Manual;

use App\Facades\JwtWebFacade;
use App\Payment;
use App\Services\Actions\RunRegularChargeForSubscriptionAction;
use App\Services\Mundipagg\CreditCardRecurrenceService;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Tests\Feature\Helper\MundipaggToken;
use Tests\Feature\Traits\CreateSubscriberTrait;
use Tests\Feature\Traits\Integration\MockPubSubTrait;
use Tests\Feature\Traits\LocalDatabaseIds;
use Tests\TestCase;

class PlatformPaymentSubscriptionTest extends TestCase
{
    use MockPubSubTrait;
    use LocalDatabaseIds;
    use CreateSubscriberTrait;

    public function test_manually_charge_failed_subscription_via_platform()
    {
        $this->withoutMiddleware();
        $this->mockPubSubWithCount(1);

        // subscriber
        $subscriberId = $this->createSubscriber($this->platformId, $this->subscriptionPlanId);

        // subscription
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

        //dump('Renew on due date: forced fail');

        CreditCardRecurrenceService::$forceFailStatusDebug = true;

        $dueDate = Carbon::now()->addDays(30); // next recurrence: D
        Carbon::setTestNow($dueDate);

        $action = new RunRegularChargeForSubscriptionAction(['subscriber_id' => $subscriberId]);
        $action();

        Carbon::setTestNow($dueDate->addDay()); // simulate late payment

        JwtWebFacade::setPayload((object) [
            'platform_id' => $this->platformId,
            'user_id' => $this->platformUserId,
        ]);

        $paymentId = Payment::where('status', 'failed')->latest('id')->first()->id;

        $result = $this->postJson("/api/payments/{$paymentId}/failed");

        $result->assertStatus(Response::HTTP_OK); // success

        $lastPaidPayment = Payment::where('status', 'paid')->latest('id')->first();
        $this->assertNotEquals($lastPaidPayment->payment_date, $lastPaidPayment->confirmed_at->format('Y-m-d'));
    }
}
