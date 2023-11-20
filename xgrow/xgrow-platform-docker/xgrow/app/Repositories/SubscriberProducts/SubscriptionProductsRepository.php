<?php

namespace App\Repositories\SubscriberProducts;

use App\Mail\SendMailBankSlip;
use App\Mail\SendMailPaymentConfirmed;
use App\Mail\SendMailRefund;
use App\Payment;
use App\PaymentPlan;
use App\Services\Contracts\SubscriptionServiceInterface;
use App\Services\EmailService;
use App\Services\LA\CacheClearService;
use App\Subscription;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Exception\GuzzleException;

/**
 *
 */
class SubscriptionProductsRepository
{
    /**
     * @var CacheClearService
     */
    private CacheClearService $cacheClearService;

    /**
     * @var SubscriptionServiceInterface
     */
    private SubscriptionServiceInterface $subscriptionService;

    /**
     * @param SubscriptionServiceInterface $subscriptionService
     * @param CacheClearService $cacheClearService
     */
    public function __construct(SubscriptionServiceInterface $subscriptionService, CacheClearService $cacheClearService)
    {
        $this->subscriptionService = $subscriptionService;
        $this->cacheClearService = $cacheClearService;
    }

    /**
     * @param string $status
     * @param int $subscriptionId
     * @return array
     * @throws GuzzleException
     */
    public function changeSubscriptionStatus(string $status, int $subscriptionId, string $platformId): array
    {
        try {

            $subscription = Subscription::where('id', $subscriptionId)
                ->where('platform_id', $platformId)
                ->first();

            if (!$subscription) {

                return ['error' => true, 'message' => 'Assinatura não encontrada', 'response' => null, 'status_code' => 403];
            }

            switch ($status) {
                case 'active':
                    $subscription->canceled_at = null;
                    $subscription->payment_pendent = null;
                    break;

                case 'canceled':
                    $subscription->canceled_at = Carbon::now();
                    break;

                case 'pending':
                    $subscription->payment_pendent = Carbon::now();
                    $subscription->canceled_at = null;
                    break;
            }

            $subscription->status = $status;
            $subscription->save();

            $this->cacheClearService->clearSubscriberCache(
                $platformId,
                null,
                $subscription->subscriber_id
            );

            return ['error' => false, 'message' => 'Produto alterado com sucesso!', 'response' => null, 'status_code' => 200];
        } catch (Exception $e) {

            return ['error' => true, 'message' => $e->getMessage(), 'response' => null, 'status_code' => 400];
        }
    }


    /**
     * @param int $subscriptionId
     * @param $canceledAt
     * @param $authUser
     * @return array
     * @throws GuzzleException
     */
    public function cancelNotRefund(int $subscriptionId, $canceledAt, $authUser): array
    {
        $subscription = Subscription::where('id', $subscriptionId)
            ->where('platform_id', $authUser->platform_id)
            ->first();

        if (!$subscription) {

            return ['error' => true, 'message' => 'Assinatura não encontrada', 'response' => null, 'status_code' => 403];
        }

        $payments = $subscription->payments;

        $firstPayment = $payments->first() ?? null;

        $isUnlimited = $firstPayment->type == Payment::TYPE_UNLIMITED;

        try {
            if ($isUnlimited) {
                $this->subscriptionService->cancelSubscriptionsAndPayments(
                    $subscription->order_number,
                    'Cancelado manualmente',
                    $authUser->id
                );
            } else {
                $this->subscriptionService->cancel($subscription, $canceledAt);
            }

            $this->cacheClearService->clearSubscriberCache(
                $authUser->platform_id,
                null,
                $subscription->subscriber->id
            );

            return [
                'error' => false,
                'message' => "O cancelamento do produto {$subscription->plan->name} do aluno {$subscription->subscriber->name} foi realizado com sucesso!",
                'response' => null,
                'status_code' => 200
            ];
        } catch (Exception $e) {

            return [
                'error' => true,
                'message' => $e->getMessage(),
                'response' => null,
                'status_code' => 400
            ];
        }
    }

    /**
     * @param string $platformId
     * @param int $paymentId
     * @return array
     */
    public function sendPurchaseProof(string $platformId, int $paymentId): array
    {
        try {
            $payment = Payment::where('id', $paymentId)->where('platform_id', $platformId)->first();

            if (!$payment) {

                return ['error' => true, 'message' => 'Pagamento não encontrado', 'response' => null, 'status_code' => 403];
            }

            $subscriber = $payment->subscriber;

            EmailService::mail(
                [$subscriber->email],
                new SendMailPaymentConfirmed($platformId, $subscriber, $payment)
            );

            return [
                'error' => false,
                'message' => 'Comprovante de confirmação de compra reenviado com sucesso!',
                'response' => null,
                'status_code' => 200
            ];
        } catch (Exception $e) {
            return [
                'error' => true,
                'message' => $e->getMessage(),
                'response' => null,
                'status_code' => 400
            ];
        }
    }

    /**
     * @param string $platformId
     * @param int $paymentId
     * @return array
     */
    public function sendBankSlip(string $platformId, int $paymentId): array
    {
        try {
            $payment = Payment::where('id', $paymentId)->where('platform_id', $platformId)->first();

            if (!$payment) {

                return ['error' => true, 'message' => 'Pagamento não encontrado', 'response' => null, 'status_code' => 403];
            }

            $subscriber = $payment->subscriber;

            EmailService::mail(
                [$subscriber->email],
                new SendMailBankSlip($platformId, $subscriber, $payment)
            );

            return [
                'error' => false,
                'message' => 'Boleto reenviado com sucesso!',
                'response' => null,
                'status_code' => 200
            ];
        } catch (Exception $e) {
            return [
                'error' => true,
                'message' => $e->getMessage(),
                'response' => null,
                'status_code' => 400
            ];
        }
    }

    /**
     * @param string $platformId
     * @param int $paymentPlanId
     * @return array
     */
    public function sendRefund(string $platformId, int $paymentPlanId): array
    {
        try {
            $paymentPlan = PaymentPlan::find($paymentPlanId);

            if (!$paymentPlan) {

                return ['error' => true, 'message' => 'Plano não encontrado', 'response' => null, 'status_code' => 403];
            }

            $payment = $paymentPlan->payment;
            $subscriber = $payment->subscriber;

            EmailService::mail(
                [$subscriber->email],
                new SendMailRefund($platformId, $subscriber, $paymentPlan, $payment->order_code, $paymentPlan->plan_value, null, $payment->updated_at)
            );

            return [
                'error' => false,
                'message' => 'Comprovante de estorno reenviado com sucesso!',
                'response' => null,
                'status_code' => 200
            ];
        } catch (Exception $e) {

            return [
                'error' => true,
                'message' => $e->getMessage(),
                'response' => null,
                'status_code' => 400
            ];
        }
    }

    /**
     * @param $paymentPlanId
     * @return array
     */
    public function refundProof($paymentPlanId): array
    {
        $paymentPlan = PaymentPlan::where('id', $paymentPlanId)->where('status', 'refunded')->first();

        $payment = $paymentPlan->payment;
        $subscriber = $payment->subscriber;
        $plan = ($payment->type === 'R') ? [$payment->recurrences[0]->plan] : $paymentPlan->plan;

        return [
            'refund' => [
                'total' => formatCoin($payment->price),
                'code' => $payment->order_code
            ],
            'subscriber' => [
                'name' => $subscriber->name ?? '',
                'email' => $subscriber->email ?? '',
                'document_type' => $subscriber->document_type ?? '',
                'document_number' => $subscriber->document_number ?? '',
                'cellphone' => $subscriber->cel_phone ?? '',
            ],
            'purchase' => [
                'product' => $plan->name,
                'total' => formatCoin($plan->price)
            ]
        ];
    }

    /**
     * @param int $subscriberId
     * @param string $platformId
     * @param array $productsId
     * @return mixed
     */
    public function listProductsBySubscriber(int $subscriberId, string $platformId, array $productsId, int $offset)
    {
        try {

            $products = $this->queryProductsPlansBySubscriber($subscriberId, $platformId, $productsId)
                ->when($productsId, function ($query, $productsId) {
                    $query->whereIn('products.id', $productsId);
                })->paginate($offset);

            return [
                'error' => false,
                'message' => 'Dados carregados com sucesso!',
                'response' => ['data' => $products],
                'status_code' => 200
            ];
        } catch (Exception $e) {
            return [
                'error' => true,
                'message' => $e->getMessage(),
                'response' => null,
                'status_code' => 400
            ];
        }
    }

    /**
     * @param int $subscriberId
     * @param string $platformId
     * @param array $plansId
     * @return mixed
     */
    public function listPlansBySubscriber(int $subscriberId, string $platformId, array $plansId)
    {
        try {

            $data = $this->queryProductsPlansBySubscriber($subscriberId, $platformId, $plansId)
                ->when($plansId, function ($query, $plansId) {
                    $query->whereIn('plans.id', $plansId);
                })->get();

            $plans = [];

            foreach ($data as $value) {
                $plans[] = [
                    'plan_id' => $value->plan_id,
                    'plans_name' => mb_convert_case($value->plans_name, MB_CASE_TITLE, 'UTF-8'),
                ];
            }

            return [
                'error' => false,
                'message' => 'Dados carregados com sucesso!',
                'response' => ['data' => $plans],
                'status_code' => 200
            ];
        } catch (Exception $e) {
            return [
                'error' => true,
                'message' => $e->getMessage(),
                'response' => null,
                'status_code' => 400
            ];
        }
    }

    /**
     * @param int $subscriberId
     * @param string $platformId
     * @return mixed
     */
    private function queryProductsPlansBySubscriber(int $subscriberId, string $platformId)
    {
        return  Subscription::select(
            'payments.id as payment_id',
            'payments.cancellation_reason AS payment_cancellation_reason',
            'payments.type AS payment_type',
            'payments.order_number as payment_order_number',
            'payments.order_code as payment_order_code',
            'payments.status as payment_status',
            'payments.type_payment as payment_type_payment',
            'payment_plan.id as payment_plan_id',
            'plans.id as plan_id',
            'plans.name AS plans_name',
            'subscriptions.id as subscriptions_id',
            'subscriptions.subscriber_id as subscriptions_subscriber_id',
            'subscriptions.status as subscriptions_status',
            'subscriptions.created_at as subscriptions_created_at',
            'subscriptions.canceled_at as subscriptions_canceled_at',
            'products.id AS product_id',
            'products.name AS product_name',
        )
            ->join('payments', 'payments.subscriber_id', '=', 'subscriptions.subscriber_id')
            ->join('payment_plan', 'payment_plan.payment_id', '=', 'payments.id')
            ->join('plans', 'payment_plan.plan_id', '=', 'plans.id')
            ->join('products', 'plans.product_id', '=', 'products.id')
            ->groupBy('products.id')
            ->where('payments.platform_id', '=', $platformId)
            ->where('subscriptions.subscriber_id', '=', $subscriberId);
    }
}
