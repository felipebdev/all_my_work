<?php

namespace App\Services\Subscriptions;

use App\Http\Controllers\Mundipagg\SubscriberController;
use App\Logs\XgrowLog;
use App\Payment;
use App\Plan;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Repositories\Contracts\SubscriptionRepositoryInterface;
use App\Services\Contracts\SubscriptionServiceInterface;
use App\Services\MundipaggService;
use App\Subscriber;
use App\Subscription;
use App\Utils\TriggerIntegrationJob;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PagarMe\Client;

class SubscriptionService implements SubscriptionServiceInterface {

    use TriggerIntegrationJob;

    private $subscriptionRepository;
    private $paymentRepository;

    public function __construct(
        SubscriptionRepositoryInterface $subscriptionRepository,
        PaymentRepositoryInterface $paymentRepository
    ) {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->paymentRepository = $paymentRepository;
    }

    public function cancel(Subscription $subscription, $date = null) {
        $canceledAt = ($subscription->plan->type_plan === 'R') ? Carbon::createFromFormat('d/m/Y', $date)->toDateTimeString() : null;
        $ids = $this->allSubscriptionsByOrderNumber($subscription);
        $this->setCancelDate($ids, $canceledAt);
        $this->triggerSubscriptionCanceledIntegrationJob($subscription);
    }

    /**
     * @deprecated use \App\Services\Checkout\RefundService::refund instead
     */
    public function cancelRefund(Subscription $subscription) {
        try {
            DB::beginTransaction();
                $payments = $this->paymentRepository->allByOrderNumberAndStatus(
                    $subscription->order_number,
                    ['paid']
                );

                foreach ($payments as $payment) {
                    app(SubscriberController::class)->cancelCharge(
                        Auth::user()->platform_id,
                        $payment->id,
                        new Request()
                    );
                }

                $ids = $this->allSubscriptionsByOrderNumber($subscription);
                $this->setCancelDate($ids);
                $this->paymentRepository->update(
                    ['order_number' => $subscription->order_number],
                    ['status' => 'canceled']
                );
            DB::commit();
        }
        catch(Exception $e) {
            DB::rollback();
            throw $e;
        }

        $this->triggerSubscriptionCanceledAndPaymentRefundIntegrationJob($subscription);
    }

    /**
     * @deprecated use \App\Services\Checkout\RefundService::refund instead
     */
    public function cancelRefundPix(Subscription $subscription)
    {
        try {
            DB::beginTransaction();
            $payments = $this->paymentRepository->allByOrderNumberAndStatus($subscription->order_number, ['paid']);

            foreach ($payments as $payment) {
                $this->refundPix($payment);
            }

            $ids = $this->allSubscriptionsByOrderNumber($subscription);
            $this->setCancelDate($ids);
            $this->paymentRepository->update(
                ['order_number' => $subscription->order_number],
                ['status' => 'canceled']
            );
            DB::commit();
        } catch(Exception $e) {
            DB::rollback();
            throw $e;
        }

        $this->triggerSubscriptionCanceledAndPaymentRefundIntegrationJob($subscription);
    }

    /**
     * @deprecated use \App\Services\Checkout\RefundService::refund instead
     */
    public function cancelRefundBoleto(
        Subscription $subscription,
        string $bankCode,
        string $agency,
        ?string $agencyDigit,
        string $account,
        ?string $accountDigit,
        string $documentNumber,
        string $legalName
    ) {
        try {
            DB::beginTransaction();
            $payments = $this->paymentRepository->allByOrderNumberAndStatus($subscription->order_number, ['paid']);

            foreach ($payments as $payment) {
                $this->refundBoleto(
                    $payment,
                    $bankCode,
                    $agency,
                    $agencyDigit,
                    $account,
                    $accountDigit,
                    $documentNumber,
                    $legalName
                );
            }

            $ids = $this->allSubscriptionsByOrderNumber($subscription);
            $this->setCancelDate($ids);
            $this->paymentRepository->update(
                ['order_number' => $subscription->order_number],
                ['status' => 'canceled']
            );
            DB::commit();
        } catch(Exception $e) {
            DB::rollback();
            throw $e;
        }

        $this->triggerSubscriptionCanceledAndPaymentRefundIntegrationJob($subscription);
    }

    /**
     * @deprecated use \App\Services\Checkout\RefundService::refund instead
     */
    public function refundPix(Payment $payment)
    {
        $pagarme = new Client(env('PAGARME_API_KEY'));

        $refundedTransaction = $pagarme->transactions()->refund([
            'id' => $payment->charge_id,
        ]);

        if ($refundedTransaction->status == 'refunded') {
            $payment->status = Payment::STATUS_CANCELED;
            $payment->save();

            try {
                $this->cancelSubscriptionByPayment($payment);
            }
            catch(Exception $e) {}
        }

        return $refundedTransaction;
    }

    /**
     * @deprecated use \App\Services\Checkout\RefundService::refund instead
     */
    public function refundBoleto(
        Payment $payment,
        string $bankCode,
        string $agency,
        ?string $agencyDigit,
        string $account,
        ?string $accountDigit,
        string $documentNumber,
        string $legalName
    )
    {
        $pagarme = new Client(env('PAGARME_API_KEY'));

        $refundedTransaction = $pagarme->transactions()->refund([
            'id' => $this->getPagarmeIdFromMundipagg($payment->order_id),
            'bank_code' => $bankCode,
            'agencia' => $agency,
            'agencia_dv' => $agencyDigit,
            'conta' => $account,
            'conta_dv' => $accountDigit,
            'document_number' => $documentNumber,
            'legal_name' => $legalName,
        ]);

        if ($refundedTransaction->status == 'refunded') {
            $payment->status = Payment::STATUS_CANCELED;
            $payment->save();

            try {
                $this->cancelSubscriptionByPayment($payment);
            }
            catch(Exception $e) {}
        }

        return $refundedTransaction;
    }

    public function hasActiveSubscription($subscriber_id, $platform_id, $plan_id): bool
    {
        return Subscription::where('subscriber_id', '=', $subscriber_id)
            ->where('platform_id', '=', $platform_id)
            ->where('plan_id', '=', $plan_id)
            ->whereNull('canceled_at')
            ->exists();
    }

    public function markSubscriptionWithFailedPayment(Subscriber $subscriber, Plan $plan): bool
    {
        $return = false;
        $activeSubscription = Subscription::where('subscriber_id', '=', $subscriber->id)
            ->where('platform_id', '=', $subscriber->platform_id)
            ->where('plan_id', '=', $plan->id)
            ->whereNull('canceled_at')
            ->first();

        if( $activeSubscription ) {
            $activeSubscription->status = Subscription::STATUS_FAILED;
            $activeSubscription->status_updated_at = \Carbon\Carbon::now();
            $return = $activeSubscription->save();
        }
        return $return;
    }

    public function cancelSubscription(
        Subscriber $subscriber,
        Plan $plan,
        ?string $subscriptionCancellationReason = null
    ): bool {
        $return = false;
        $activeSubscription = Subscription::where('subscriber_id', '=', $subscriber->id)
            ->where('platform_id', '=', $subscriber->platform_id)
            ->where('plan_id', '=', $plan->id)
            ->whereNull('canceled_at')->first();

        if ($activeSubscription) {
            $activeSubscription->status = Subscription::STATUS_CANCELED;
            $activeSubscription->status_updated_at = \Carbon\Carbon::now();
            $activeSubscription->canceled_at = Carbon::now();
            $return = $activeSubscription->save();
        }
        return $return;
    }

    public function enableSubscriptionByPayment(Payment $payment) {
        if ($payment->status !== Payment::STATUS_PAID) return;
        try {
            if (!empty($payment->order_number)) {
                $this->subscriptionRepository->update(
                    ['order_number' => $payment->order_number],
                    [
                        'canceled_at' => null,
                        'status' => Subscription::STATUS_ACTIVE,
                        'status_updated_at' => Carbon::now(),
                    ],
                    $payment->platform_id
                );
            }
            else {
                $startDate = Carbon::createFromTimeString($payment->created_at, config('app.timezone'))->subMinutes(2);
                $endDate = Carbon::createFromTimeString($payment->created_at, config('app.timezone'))->addMinutes(2);
                $plans = $payment->plans->pluck('id')->toArray();

                $subscriptions = $this->subscriptionRepository->allBySubscriberAndPlans(
                    $payment->subscriber->id,
                    $plans,
                    $payment->platform_id,
                    [
                       'canceled_at' => ['op' => '!=', 'value' => null],
                       'raw' => "(subscriptions.created_at >= '{$startDate}' AND
                                subscriptions.created_at <= '{$endDate}')"
                    ],
                    ['id'],
                    count($plans)
                );

                $ids = $subscriptions->pluck('id')->toArray();
                if (!empty($ids)) {
                    $this->subscriptionRepository->updateById(
                        $ids,
                        [
                            'canceled_at' => null,
                            'status' => Subscription::STATUS_ACTIVE,
                            'status_updated_at' => Carbon::now()
                        ]
                    );
                }
            }
        }
        catch(Exception $e) {
            Log::error(
                'Can not enable subscription > ',
                [
                    'error' => $e->getMessage(),
                    'payload' => [
                        'payment_id' => $payment->id
                    ]
                ]
            );

            throw $e;
        }
    }

    public function cancelSubscriptionByPayment(Payment $payment) {
        try {
            $now = Carbon::now();
            $orderNumber = $payment->order_number;
            $transactionCode = ($payment->type === Payment::TYPE_SALE) ?
                $payment->order_code :
                null;

            if ($payment->type === Payment::TYPE_UNLIMITED) {
                if (!empty($payment->installment_number)) {
                    $transactionCode = $payment->order_code;
                    if ($payment->installment_number !== 1) {
                        $parentId = $payment->id - ($payment->installment_number - 1); //first installment payment
                        $transactionCode = $this->paymentRepository
                            ->findById($parentId, ['order_code'])
                            ->order_code;
                    }
                }
            }

            if (!empty($orderNumber)) {
                $this->subscriptionRepository->update(
                    ['order_number' => $orderNumber],
                    [
                        'canceled_at' => $now,
                        'status' => Subscription::STATUS_CANCELED,
                        'status_updated_at' => $now
                    ],
                    $payment->platform_id
                );
            }
            else if (!empty($transactionCode)) {
                $this->subscriptionRepository->update(
                    ['gateway_transaction_id' => $transactionCode],
                    [
                        'canceled_at' => $now,
                        'status' => Subscription::STATUS_CANCELED,
                        'status_updated_at' => $now
                    ],
                    $payment->platform_id
                );
            }
            else {
                $subscriber = $payment->subscriber;
                $plan = $payment->plans()->first();
                $this->cancelSubscription($subscriber, $plan);
            }
        }
        catch(Exception $e) {
            XgrowLog::xError(
                'Can not cancel subscription > ',
                $e,
                $payment->toArray()
            );

            throw $e;
        }
    }

    public function cancelSubscriptionsAndPayments(
        string $orderNumber,
        ?string $cancellationReason = null,
        ?int $cancellationUserId = null
    ): bool {
        $allPayments = Payment::where('order_number', $orderNumber)->with('plans')->get();

        $subscriber = $allPayments->first()->subscriber;

        DB::beginTransaction();

        $plans = $allPayments->map->plans->flatten(1)->unique('id');

        foreach ($plans as $plan) {
            $canceled = $this->cancelSubscription($subscriber, $plan, $cancellationReason);
            if (!$canceled) {
                DB::rollBack();
                return false;
            }
        }

        $status = [
            Payment::STATUS_FAILED,
            Payment::STATUS_PENDING,
        ];

        $payments = $allPayments->whereIn('status', $status);
        foreach ($payments as $payment) {
            $payment->status = Payment::STATUS_CANCELED;
            $payment->cancellation_at = Carbon::now();
            $payment->cancellation_reason = $cancellationReason;
            if ($cancellationUserId) {
                $payment->cancellation_user = $cancellationUserId;
            }

            if (is_null($cancellationUserId)) {
                $payment->notes = implode('; ', array_filter([$payment->notes, 'Automatically canceled']));
            }

            $canceled = $payment->save();
            if (!$canceled) {
                DB::rollBack();
                return false;
            }
        }

        DB::commit();

        return true;
    }

    private function setCancelDate(array $ids, $date = null) {
        if (empty($date) || !validateDate($date)) {
            $date = Carbon::now();
        }

        return $this->subscriptionRepository->updateById(
            $ids,
            [
                'status' => Subscription::STATUS_CANCELED,
                'status_updated_at' => now(),
                'canceled_at' => $date
            ]
        );
    }

    private function allSubscriptionsByOrderNumber(Subscription $subscription) {
        $ids = [];
        if (!empty($subscription->order_number)) {
            $subscriptions = $this->subscriptionRepository->allByOrderNumber(
                $subscription->order_number,
                ['id', 'order_number']
            );

            foreach ($subscriptions as $subscpt) {
                $ids[] = $subscpt->id;
            }
        }
        else {
            $ids[] = $subscription->id;
        }

        if (empty($ids)) throw new ModelNotFoundException();

        return $ids;
    }

    /**
     * Get Pagar.me code from Mundipagg
     *
     * @param $orderId
     * @return string|null
     * @throws \MundiAPILib\APIException
     */
    private function getPagarmeIdFromMundipagg($orderId): ?string
    {
        $mundipaggService = new MundipaggService(null);
        $orderId = $mundipaggService->getClient()->getOrders()->getOrder($orderId);
        $gatewayId = $orderId->charges[0]->gatewayId ?? null;
        return $gatewayId;
    }

    /**
     * @param string $orderCode
     * @throws Illuminate\Database\Eloquent\ModelNotFoundException
     * @return Illuminate\Database\Eloquent\Model
     */
    private function getPaymentByOrderCode(string $orderCode): Model
    {
        return $this->paymentRepository
            ->getModel()
            ->where('payments.order_code', '=', $orderCode)
            ->firstOrFail();
    }

    private function triggerSubscriptionCanceledAndPaymentRefundIntegrationJob(Subscription $subscription)
    {
        try {
            $payment = $this->getPaymentByOrderCode($subscription->gateway_transaction_id);
            $this->triggerPaymentRefundEvent($payment);
            $this->triggerSubscriptionCanceledEvent($payment);
        } catch (Exception $e) {}
    }

    private function triggerSubscriptionCanceledIntegrationJob(Subscription $subscription)
    {
        try {
            $payment = $this->getPaymentByOrderCode($subscription->gateway_transaction_id);
            $this->triggerSubscriptionCanceledEvent($payment);
        } catch (Exception $e) {}
    }
}
