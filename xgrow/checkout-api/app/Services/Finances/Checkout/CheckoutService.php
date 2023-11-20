<?php

namespace App\Services\Finances\Checkout;

use App\Payment;
use App\Plan;
use App\Platform;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Repositories\Contracts\SubscriptionRepositoryInterface;
use App\Services\Charges\RecurrenceService;
use App\Services\Finances\Customer\MundipaggCustomerService;
use App\Services\Finances\Objects\Constants;
use App\Services\Finances\Objects\OrderInfo;
use App\Services\Finances\Payment\PaymentOrderFactory;
use App\Services\Finances\PaymentStoreService;
use App\Services\Mundipagg\Objects\OrderResult;
use App\Subscriber;
use App\Subscription;
use App\Utils\TriggerIntegrationJob;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use MundiAPILib\Models\CreateOrderRequest;

use function app;
use function now;
use function resolve;

class CheckoutService
{
    use TriggerIntegrationJob;

    private $transactionService;

    private bool $isUpsell = false;

    /**
     * @param  bool  $isUpsell
     * @return $this
     */
    public function setIsUpsell(bool $isUpsell = true): self
    {
        $this->isUpsell = $isUpsell;
        return $this;
    }

    /**
     * @param  \App\Services\Finances\Objects\OrderInfo  $orderInfo
     * @param  \App\Subscriber  $subscriber
     * @return mixed
     * @throws \Exception
     *
     * @note Mutates subscriber in the process
     */
    public function process(OrderInfo $orderInfo, Subscriber $subscriber): OrderResult
    {
        $paymentMethod = PaymentOrderFactory::getPaymentMethod($orderInfo->getPaymentMethod());
        $orderResult = $paymentMethod->order($orderInfo, $subscriber);

        //order paid or pending when not closed.
        if ($orderResult->getOrderResponse()) {
            $this->expireOldPayments($orderInfo);
            $this->confirmCheckout($orderInfo, $subscriber, $orderResult);
        }

        return $orderResult;
    }

    public function confirmCheckout(
        OrderInfo $orderInfo,
        Subscriber $subscriber,
        OrderResult $orderResult
    ): bool {
        $orderResponse = $orderResult->getOrderResponse();
        $plan = $orderInfo->finder->rememberPlan();

        $orderNumber = strtoupper(uniqid());
        $chargeDate = Carbon::now();
        if ($plan->freedays_type == Plan::FREE_DAYS_TYPE_FREE && $plan->freedays > 0) {
            $chargeDate = Carbon::now()->addDays($plan->freedays);
        }

        $platform = Platform::find($subscriber->platform_id);
        $clientTaxTransaction = ($platform) ? ($platform->client->tax_transaction ?? 1.5) : 1.5;

        $isMultimeans = $orderInfo->getPaymentMethod() == Constants::XGROW_MULTIMEANS;

        //store payment/payments
        $payments = (new PaymentStoreService())
            ->setPaymentSource(Payment::PAYMENT_SOURCE_CHECKOUT)
            ->withOrderBumpsBag($orderInfo->getOrderBumpsBag())
            ->setIsUpsell($orderInfo->isUpsell())
            ->setIsMultimeans($isMultimeans)
            ->storePayments(
                $subscriber,
                $orderResult,
                $chargeDate,
                $orderNumber,
                $clientTaxTransaction
            );

        $firstPayment = $payments->first();

        //Generate Unlimited sale pending payments
        $isUnlimitedSale = $orderResponse->metadata['unlimited_sale'] ?? false;
        if ($isUnlimitedSale) {
            //Saves remaining payments from the unlimited sale
            foreach ($orderResponse->charges as $cod => $charge) {
                for ($i = 1; $i < $orderResponse->metadata['total_installments']; $i++) {
                    //Payment date + 30 days
                    $chargeDate = $chargeDate->addDays(30);
                    (new PaymentStoreService)
                        ->setIsUpsell($orderInfo->isUpsell())
                        ->withOrderBumpsBag($orderInfo->getOrderBumpsBag())
                        ->storePendingPayments(
                            $orderInfo,
                            $subscriber,
                            $orderResponse,
                            $chargeDate,
                            $firstPayment,
                            $i + 1,
                            $orderResult,
                            $clientTaxTransaction
                        );
                }
            }
        }

        // Create recurrence for all subscription plans, including order bumps

        $allPlans = array_merge([$plan], $orderInfo->getOrderBumpsBag()->getOrderBumpsPlans());

        foreach ($allPlans as $index => $plan) {
            if ($plan->type_plan != Plan::PLAN_TYPE_SUBSCRIPTION) {
                continue;
            }

            $recurrenceService = new RecurrenceService();

            if ($orderInfo->getPaymentMethod() == Constants::XGROW_CREDIT_CARD) {
                $firstCcInfo = $orderInfo->getCcInfo()[0];

                $isMainPlan = $index == 0;
                $originalInstallments = $isMainPlan ? ($firstCcInfo['installment'] ?? 1) : 1;

                $paymentDate = $chargeDate;
            } else {
                $originalInstallments = 1;

                $paymentDate = null;
            }

            $affiliate = $orderInfo->finder->rememberAffiliate();
            if ($affiliate) {
                $recurrenceService->withAffiliateId($affiliate->id);
            }

            $recurrence = $recurrenceService->createSubscriptionRecurrence(
                $subscriber,
                $plan,
                $orderInfo->getPaymentMethod(),
                $originalInstallments,
                $orderNumber,
                $chargeDate,
                $paymentDate
            );

            if ($orderResponse) {
                $firstPayment->recurrences()->attach($recurrence);
            }
        }

        $paymentMethod = PaymentOrderFactory::getPaymentMethod($orderInfo->getPaymentMethod());

        $confirmOrder = $paymentMethod->confirmOrder($orderResponse, $subscriber, ...$payments->all());

        $pendingPayment = $confirmOrder->isPendingPayment ? date_format(now(), 'Y-m-d') : null;

        //save subscription
        $this->saveSubscription($orderInfo, $subscriber, $orderResponse->code, $pendingPayment, $orderNumber);

        $subscriber->save(); //Save subscriber modifications: plan_id, customer_id, etc

        return $confirmOrder->isSuccessful;
    }

    private function saveSubscription(
        OrderInfo $orderInfo,
        Subscriber $subscriber,
        $code = null,
        $payment_pendent = null,
        $orderNumber = null
    ) {
        $mainPlan = $orderInfo->finder->rememberPlan();
        $orderBumps = $orderInfo->getOrderBumpsBag()->getOrderBumpsPlans();

        $plans = array_merge([$mainPlan], $orderBumps);

        foreach ($plans as $plan) {
            $subscription = Subscription::firstOrNew([
                    'platform_id' => $orderInfo->getPlatformId(),
                    'plan_id' => $plan->id,
                    'subscriber_id' => $subscriber->id,
                    'canceled_at' => null,
                    'order_number' => $orderNumber
                ]
            );
            $subscription->platform_id = $orderInfo->getPlatformId();
            $subscription->plan_id = $plan->id;
            $subscription->subscriber_id = $subscriber->id;
            $subscription->gateway_transaction_id = $code;
            $subscription->payment_pendent = $payment_pendent;
            $subscription->status = ($payment_pendent !== null)
                ? Subscription::STATUS_PENDING_PAYMENT
                : Subscription::STATUS_ACTIVE;
            $subscription->status_updated_at = \Carbon\Carbon::now();
            $subscription->order_number = $orderNumber ?? null;
            $subscription->save();
        }
    }

    /**
     * Expire pending payments (boleto/pix) containing any of the planIds (product + OrderBumps)
     *
     * @param  \App\Services\Finances\Objects\OrderInfo  $orderInfo
     */
    private function expireOldPayments(OrderInfo $orderInfo)
    {
        $platformId = $orderInfo->getPlatformId();
        $planIds = $orderInfo->getAllPlanIds();
        $subscriberId = $orderInfo->getSubscriberId();

        try {
            /** @var PaymentRepositoryInterface $paymentRepository */
            $paymentRepository = app()->make(PaymentRepositoryInterface::class);

            $pendingPayments = $paymentRepository
                ->getPendingPaymentsOfPlansAndSubscriber($platformId, $planIds, $subscriberId);

            $paymentIds = $pendingPayments->pluck('id')->toArray();

            $paymentRepository->batchUpdate($paymentIds, ['status' => Payment::STATUS_EXPIRED]);

            if ($pendingPayments->count() > 0) {
                $orderNumbers = $pendingPayments->pluck('order_number')->toArray();
                $this->cancelExpiredSubscriptionsByOrderNumbers($platformId, $orderNumbers);
            }
        } catch (Exception $e) {
            Log::error(
                'MundipaggCheckoutController@expireOldPayments > ',
                ['error' => $e->getMessage()]
            );
        }
    }

    private function cancelExpiredSubscriptionsByOrderNumbers(string $platformId, array $orderNumbers)
    {
        //Update all subscriptions of expired payments to cancalled
        /** @var SubscriptionRepositoryInterface $subscriptionRepository */
        $subscriptionRepository = app()->make(SubscriptionRepositoryInterface::class);

        $subscriptionsIds = [];
        foreach ($orderNumbers as $orderNumber) {
            $ids = $subscriptionRepository->allByOrderNumber($orderNumber, ['id'], $platformId)->pluck('id')->toArray();
            $subscriptionsIds = array_merge($subscriptionsIds, $ids);
        }

        $subscriptionRepository->updateById(
            $subscriptionsIds,
            [
                'canceled_at' => Carbon::now(),
                'status' => Subscription::STATUS_CANCELED,
                'status_updated_at' => Carbon::now(),
                'cancellation_reason' => 'Outra compra efetuada',
            ],
            $platformId
        );
    }

}
