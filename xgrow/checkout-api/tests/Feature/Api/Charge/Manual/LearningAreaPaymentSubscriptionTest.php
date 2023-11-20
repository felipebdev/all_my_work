<?php

namespace Tests\Feature\Api\Charge\Manual;

use App\Facades\JwtPlatformFacade;
use App\Payment;
use App\Services\Actions\RunRegularChargeForSubscriptionAction;
use App\Services\Mundipagg\CreditCardRecurrenceService;
use App\Subscriber;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Tests\Feature\Helper\MundipaggToken;
use Tests\Feature\Traits\CreateSubscriberTrait;
use Tests\Feature\Traits\Integration\MockPubSubTrait;
use Tests\Feature\Traits\LocalDatabaseIds;
use Tests\TestCase;

class LearningAreaPaymentSubscriptionTest extends TestCase
{
    use MockPubSubTrait;
    use LocalDatabaseIds;
    use CreateSubscriberTrait;

    public function test_can_manually_pay_subscription_via_learning_area()
    {
        $this->withoutMiddleware();
        $this->mockPubSubWithCount(1);

        // subscriber
        $subscriberId = $this->createSubscriber($this->platformId, $this->subscriptionPlanId);
        $subscriber = self::$lastSubscriberRequest;

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

        // add a new credit card

        JwtPlatformFacade::setPlatformId($this->platformId)->setSubscriber(Subscriber::findOrFail($subscriberId));

        $result = $this->postJson('/api/creditcard', [
            'number' => $this->faker->creditCardNumber('Visa'),
            'holder_name' => 'JOSE A SILVA',
            'holder_document' => $subscriber['document_number'],
            'exp_month' => '01',
            'exp_year' => '2030',
            'brand' => 'Visa',
            'cvv' => '651',
        ]);

        $data = $result->json();

        // use credit card to pay

        $result = $this->postJson('api/payments/recurrence', [
            'credit_card_id' => $data['id'],
            'payment_id' => Payment::latest('id')->first()->id,
        ]);

        $result->assertStatus(Response::HTTP_NO_CONTENT); // success

        $lastPaidPayment = Payment::where('status', 'paid')->latest('id')->first();
        $this->assertNotEquals($lastPaidPayment->payment_date, $lastPaidPayment->confirmed_at->format('Y-m-d'));
    }

    public function test_can_list_payments_via_learning_area()
    {
        $this->withoutMiddleware();

        JwtPlatformFacade::setPlatformId($this->platformId)->setSubscriber(Subscriber::first());

        $response = $this->get('/api/payments');

        if ($response->getStatusCode() == 409) {
            $this->markTestSkipped('Gateway problem');
        }

        $response->assertStatus(200);
    }
}
