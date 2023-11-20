<?php

namespace Tests\Feature\Api\Charge\Manual;

use App\Facades\JwtWebFacade;
use App\Payment;
use App\Services\Actions\RunRegularChargeForNoLimitAction;
use App\Services\Charges\NoLimitChargeService;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Tests\Feature\Helper\MundipaggToken;
use Tests\Feature\Traits\CreateSubscriberTrait;
use Tests\Feature\Traits\Integration\MockPubSubTrait;
use Tests\Feature\Traits\LocalDatabaseIds;
use Tests\TestCase;

class PlatformPaymentNolimitTest extends TestCase
{

    use MockPubSubTrait;
    use LocalDatabaseIds;
    use CreateSubscriberTrait;

    public function test_can_manually_charge_failed_no_limit_via_platform()
    {
        $this->withoutMiddleware();
        $this->mockPubSubWithCount(1);

        // subscriber
        $subscriberId = $this->createSubscriber($this->platformId, $this->salePlanId);

        // subscription
        $token = MundipaggToken::insufficientBalance($this->faker->creditCardNumber('MasterCard'));

        $response = $this->postJson("/api/checkout/{$this->platformId}/{$this->salePlanId}", [
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
            "plan_id" => $this->salePlanId,
            "order_bump" => []
        ]);

        if ($response->getStatusCode() == 409) {
            $this->markTestSkipped('Gateway problem');
        }

        $response->assertStatus(200);

        //dump('Renew on due date: forced fail');

        NoLimitChargeService::$forceFailStatusDebug = true;

        $dueDate = Carbon::now()->addDays(30); // next recurrence: D
        Carbon::setTestNow($dueDate);

        $action = new RunRegularChargeForNoLimitAction(['subscriber_id' => $subscriberId]);
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
