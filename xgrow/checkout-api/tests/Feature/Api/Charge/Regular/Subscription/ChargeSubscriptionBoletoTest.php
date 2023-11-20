<?php

namespace Tests\Feature\Api\Charge\Regular\Subscription;

use App\Http\Controllers\Webhooks\StatusPagarmeController;
use App\Payment;
use App\Services\Actions\ExpirePaymentsAndCancelSubscriptions;
use App\Services\Actions\RunRegularChargeForSubscriptionAction;
use Carbon\Carbon;
use Tests\Feature\Traits\CreateSubscriberTrait;
use Tests\Feature\Traits\LocalDatabaseIds;
use Tests\TestCase;

class ChargeSubscriptionBoletoTest extends TestCase
{

    use CreateSubscriberTrait;
    use LocalDatabaseIds;

    public function test_regular_charge_subscription_using_boleto_success()
    {
        $this->markTestIncomplete('Regular charge of boleto changed, fix this test');

        $this->withoutMiddleware();

        $subscriberId = $this->createSubscriber($this->platformId, $this->subscriptionPlanId);

        $response = $this->postJson("/api/checkout/{$this->platformId}/{$this->subscriptionPlanId}", [
            'payment_method' => 'boleto',
            'subscriber_id' => $subscriberId,
            'platform_id' => $this->platformId,
            'plan_id' => $this->subscriptionPlanId,
            'order_bump' => []
        ]);

        if ($response->getStatusCode() == 409) {
            $this->markTestSkipped('Gateway problem');
        }

        $response->assertStatus(200);

        /////// fake postback

        $payment = Payment::latest('id')->first();

        StatusPagarmeController::$skipPostbackSignatureValidation = true;

        $response = $this->withHeaders(['X-Hub-Signature' => '123'])
            ->json('POST', '/api/checkout/order/paid', [
                //"id" => "hook_WmekPehanFGr79y2",
                "type" => "charge.paid",
                "data" => [
                    "id" => $payment->charge_id,
                    "status" => "paid",
                    "payment_method" => "boleto",
                    "order" => [
                        "code" => $payment->order_code,
                    ],
                ],
            ]);

        /////// "renew"

        $dueDate = Carbon::now()->addDays(30)->subDays(5);

        // sending boleto 5 days before

        Carbon::setTestNow($dueDate);

        $action = new RunRegularChargeForSubscriptionAction(['subscriber_id' => $subscriberId]);
        $action();

        $this->assertDatabaseHas('payments', [
            'subscriber_id' => $subscriberId,
            'payment_source' => Payment::PAYMENT_SOURCE_AUTOMATIC,
            'type_payment' => Payment::TYPE_PAYMENT_BILLET,
            'status' => Payment::STATUS_PENDING,
        ]);


        $dueDate = Carbon::now()->addDays(5)->addWeekdays(2);

        Carbon::setTestNow($dueDate);

        $expire = new ExpirePaymentsAndCancelSubscriptions();
        $expire();
    }
}
