<?php

namespace App\Services\Finances\Refund;

use App\Exceptions\Finances\InvalidDataException;
use App\Exceptions\Finances\InvalidPaymentException;
use App\Exceptions\Finances\InvalidTwoFactorCodeException;
use App\Exceptions\Finances\RefundFailedException;
use App\Exceptions\NotImplementedException;
use App\Http\Traits\CustomResponseTrait;
use App\Mail\SendMailRefund;
use App\Mail\SendMailTwoFactorCode;
use App\Payment;
use App\PaymentPlan;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Services\Contracts\SubscriptionServiceInterface;
use App\Services\EmailService;
use App\Services\EmailTaggedService;
use App\Services\Finances\Objects\Constants;
use App\Services\Finances\PaymentGatewayRefundAdapter;
use App\Services\Finances\Refund\Contracts\RefundInterface;
use App\Services\Finances\Refund\Objects\BankRefund;
use App\Services\Finances\Refund\Objects\PaymentRefund;
use App\Services\Finances\Refund\Objects\Refunded;
use App\Services\Finances\Refund\Objects\RefundOptions;
use App\Services\Finances\Refund\Objects\RefundResponse;
use App\Services\Finances\Refund\Objects\UserInfo;
use App\Subscriber;
use App\Utils\Formatter;
use App\Utils\TriggerIntegrationJob;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

class RefundService
{
    use TriggerIntegrationJob;
    use CustomResponseTrait;

    private RefundInterface $refund;

    private SubscriptionServiceInterface $subscriptionService;

    private PaymentRepositoryInterface $paymentRepository;

    private const REFUND_PERIOD_LIMIT = [
        '' => 90, // "safe" default
        Payment::TYPE_PAYMENT_CREDIT_CARD => 300,
        Payment::TYPE_PAYMENT_BILLET => 90,
        Payment::TYPE_PAYMENT_PIX => 90,
    ];

    public function __construct(
        PaymentGatewayRefundAdapter $gatewayRefundAdapter,
        SubscriptionServiceInterface $subscriptionService,
        PaymentRepositoryInterface $paymentRepository
    ) {
        $driver = $gatewayRefundAdapter->driver();
        if (!$driver instanceof RefundInterface) {
            throw new NotImplementedException('Refund not implemented by driver: '.$gatewayRefundAdapter->getDefaultDriver());
        }

        $this->refund = $driver;
        $this->subscriptionService = $subscriptionService;
        $this->paymentRepository = $paymentRepository;
    }

    /**
     * @param  \App\Services\Finances\Refund\Objects\UserInfo  $userInfo
     * @param  \App\Services\Finances\Refund\Objects\PaymentRefund  $paymentRefund
     * @param  \App\Services\Finances\Refund\Objects\RefundOptions|null  $refundOptions
     * @param  \App\Services\Finances\Refund\Objects\BankRefund|null  $bankData
     * @return RefundResponse[]
     * @throws \App\Exceptions\Finances\InvalidPaymentException
     * @throws \App\Exceptions\Finances\RefundFailedException
     * @throws \App\Exceptions\Finances\TransactionNotFoundException
     */
    public function refundUser(
        UserInfo $userInfo,
        PaymentRefund $paymentRefund,
        RefundOptions $refundOptions,
        ?BankRefund $bankData = null
    ): array
    {
        if ($paymentRefund->isPartial) {
            $paymentPlan = PaymentPlan::find($paymentRefund->paymentPlanId);
            $paymentId = $paymentPlan->payment_id;
        } else {
            $paymentPlan = null;
            $paymentId = $paymentRefund->paymentId;
        }

        $this->validatePlatformPayment($userInfo->platformId, $paymentId);

        $payment = Payment::findOrFail($paymentId);
        $subscriber = $payment->subscriber;

        if ($paymentRefund->paymentMethod == Constants::XGROW_BOLETO) {
            $this->validateSubscriberDocumentBank($subscriber, $bankData);
        }

        $isUnlimited = $payment->type == Payment::TYPE_UNLIMITED;
        $isSubscription = $payment->type == Payment::TYPE_SUBSCRIPTION;
        $fullReturnUnlimitedSell = $isUnlimited && $refundOptions->refundAll;


        if ($paymentRefund->isPartial) {
            if ($fullReturnUnlimitedSell) {
                $refunds = $this->refundUnlimitedPartial($paymentRefund, $bankData);
            } else {
                $refunds = $this->refundSinglePartial($paymentRefund, $bankData);
            }

            $shouldCancelPendingPayments = $isSubscription || $fullReturnUnlimitedSell;
            $this->markPartialPaymentsRefunded(
                $userInfo->userId,
                $paymentRefund->paymentPlanId,
                $paymentRefund->reason,
                $shouldCancelPendingPayments,
                ...$refunds
            );

            if (!$isUnlimited || $fullReturnUnlimitedSell) {
                $subscriber = $payment->subscriber;
                $plan = $paymentPlan->plan;

                $this->subscriptionService->cancelSubscription($subscriber, $plan, $paymentRefund->reason);
            }
        } else {
            if ($fullReturnUnlimitedSell) {
                $refunds = $this->refundUnlimitedFull($paymentRefund, $bankData);
            } else {
                $refunds = $this->refundSingleFull($paymentRefund, $bankData);
            }

            if ($paymentRefund->isPartial) {
                $plan = $paymentPlan->plan;
                $this->markPaymentsRefunded($userInfo->userId, $paymentRefund->reason, ...$refunds);

                if (!$isUnlimited || $fullReturnUnlimitedSell) {
                    $this->subscriptionService->cancelSubscriptionsAndPayments(
                        $payment->order_number,
                        $paymentRefund->reason,
                        $userInfo->userId
                    );
                }
            }
        }

        $this->triggerPaymentRefundEvent($payment);

        $this->sendRefundEmail($payment, $refunds[0]->refundResponse, $paymentPlan);

        return array_map(fn(Refunded $refund) => $refund->refundResponse, $refunds);
    }


    public function refundStudent(
        $subscribers,
        PaymentRefund $paymentRefund,
        RefundOptions $refundOptions,
        $token,
        ?BankRefund $bankData = null
    ): array {

        $this->validateStudentPayment($subscribers, $paymentRefund->paymentId, $token);

        $payment = Payment::findOrFail($paymentRefund->paymentId);

        // verificar a data de limite de 7 dias para o aluno estornar
        $limitDate = Carbon::createFromFormat('Y-m-d', $payment->payment_date)->addDays(7);
        $currentDate = Carbon::now();

        if ($currentDate->gt($limitDate)) {
            throw new InvalidPaymentException('Refund deadline exceeded.');
        }

        $isUnlimited = $payment->type == Payment::TYPE_UNLIMITED;
        $fullReturnUnlimitedSell = $isUnlimited && $refundOptions->refundAll;

        if ($fullReturnUnlimitedSell) {
            $refunds = $this->refundUnlimitedFull($paymentRefund, $bankData);
        } else {
            $refunds = $this->refundSingleFull($paymentRefund, $bankData);
        }

        $this->markPaymentsRefundedByStudent($paymentRefund->reason, ...$refunds);

        if (!$isUnlimited || $fullReturnUnlimitedSell) {
            $this->subscriptionService->cancelSubscriptionsAndPayments(
                $payment->order_number,
                $paymentRefund->reason,
            );
        }

        $this->triggerPaymentRefundEvent($payment);

        $this->sendRefundEmail($payment, $refunds[0]->refundResponse);

        return array_map(fn(Refunded $refund) => $refund->refundResponse, $refunds);
    }

    /**
     * @param App\Http\Requests\SendTwoFactorCodeRequest $request
     * @return array
     * @throws \App\Exceptions\Finances\InvalidDataException
     */
    public function sendTwoFactorCode($request): array
    {

        $data = $this->paymentRepository->getByOrderCodeAndSubscriberMail($request->order_code, $request->email);
        if (!$data) {
            throw new InvalidDataException($request->order_code.', '.$request->email);
        }

        $pin = $this->paymentRepository->putTwoFactorCode($data['id']);

        //enviar email com o código
        $mail = new SendMailTwoFactorCode($data, $pin);
        EmailTaggedService::mail($data['platform_id'], 'UPCOMING_BOLETO_PIX', $mail, [$request->email]);


        return [];

    }

    /**
     * @param  App\Http\Requests\CheckTwoFactorCodeRequest  $request
     * @return array
     */
    public function checkTwoFactorCode($request): array
    {
        $data = $this->paymentRepository->getByOrderCodeAndSubscriberMail($request->order_code, $request->email);
        if (!$data) {
            $message = 'Order with order code '.$request->order_code.' and email '.$request->email.' not found.';
            throw new InvalidDataException($message);
        }

        if ($data['two_factor_code'] != $request->code) {
            $message = 'Two factor code not match: '.$request->code;
            throw new InvalidTwoFactorCodeException($message);
        }

        $limitDate = Carbon::createFromFormat('Y-m-d H:i:s', $data['two_factor_expires_at']);
        $currentDate = Carbon::now();

        if ($currentDate->gt($limitDate)) {
            throw new InvalidPaymentException('Two factor date expirated.');
        }

        return [$data];
    }

    /**
     * @param  \App\Services\Finances\Refund\Objects\PaymentRefund  $paymentRefund
     * @param  \App\Services\Finances\Refund\Objects\BankRefund|null  $bankRefund
     * @return Refunded[]
     * @throws \App\Exceptions\Finances\RefundFailedException
     * @throws \App\Exceptions\Finances\TransactionNotFoundException
     */
    private function refundUnlimitedFull(PaymentRefund $paymentRefund, ?BankRefund $bankRefund = null): array
    {
        $paidPayments = $this->listPaidPaymentsRelatedToPayment($paymentRefund->paymentId);

        $notRefundable = $paidPayments->filter(function (Payment $payment) {
            return !$this->paymentCanBeRefunded($payment);
        });

        if ($notRefundable->count() > 0) {
            throw new RefundFailedException('Existem pagamentos que não podem ser estornados pois o período limite de dias foi ultrapassado.');
        }

        $refunds = [];
        foreach ($paidPayments as $payment) {
            $paidPaymentRefund = $paymentRefund->cloneWith(['paymentId' => $payment->id]);

            $refund = $this->refund->refund($paidPaymentRefund, $bankRefund);

            $refunds[] = Refunded::create($payment, $refund);
        }

        return $refunds;
    }

    protected function refundUnlimitedPartial(PaymentRefund $paymentRefund, ?BankRefund $bankData): array
    {
        $paymentPlanRefund = PaymentPlan::find($paymentRefund->paymentPlanId);
        $payment = $paymentPlanRefund->payment;

        $paidPayments = $this->listPaidPaymentsRelatedToPayment($payment->id);

        $notRefundable = $paidPayments->filter(function (Payment $payment) {
            return !$this->paymentCanBeRefunded($payment);
        });

        if ($notRefundable->count() > 0) {
            throw new RefundFailedException('Existem pagamentos que não podem ser estornados pois o período limite de dias foi ultrapassado.');
        }

        $paymentPlans = PaymentPlan::query()
            ->whereIn('payment_id', $paidPayments->pluck('id')->toArray())
            ->where('plan_id', $paymentPlanRefund->plan_id)
            ->get();

        $refunds = [];
        foreach ($paymentPlans as $paymentPlan) {
            $paidPaymentRefund = $paymentRefund->cloneWith(['paymentPlanId' => $paymentPlan->id]);

            $refund = $this->refund->refundPartial($paidPaymentRefund, $bankData);

            $payment = $paymentPlan->payment;

            $refunds[] = Refunded::create($payment, $refund);
        }

        return $refunds;
    }

    protected function refundSinglePartial(PaymentRefund $paymentRefund, ?BankRefund $bankData): array
    {
        $paymentPlan = PaymentPlan::find($paymentRefund->paymentPlanId);

        $payment = $paymentPlan->payment;

        if (!$this->paymentCanBeRefunded($payment)) {
            $message = "O pagamento não pode ser estornado pois o período limite de dias foi ultrapassado. ({$paymentPlan->id})";
            throw new RefundFailedException($message);
        }

        $refund = $this->refund->refundPartial($paymentRefund, $bankData);

        $refunds = [Refunded::create($payment, $refund)];

        return $refunds;
    }

    protected function refundSingleFull(PaymentRefund $paymentRefund, ?BankRefund $bankData): array
    {
        $payment = Payment::findOrFail($paymentRefund->paymentId);

        if (!$this->paymentCanBeRefunded($payment)) {
            throw new RefundFailedException('O pagamento não pode ser estornado pois o período limite de dias foi ultrapassado.');
        }

        $refund = $this->refund->refund($paymentRefund, $bankData);

        $refunds = [Refunded::create($payment, $refund)];

        return $refunds;
    }

    private function paymentCanBeRefunded(Payment $payment): bool
    {
        $paymentDate = Carbon::createFromFormat('Y-m-d', $payment->payment_date)
            ->setTime(0, 0);

        $paymentMethod = $payment->type_payment ?? '';

        if ($paymentMethod == '') {
            Log::warning('Refund period limit not defined for payment method', [
                'payment_method' => $paymentMethod,
            ]);
        }

        $limit = self::REFUND_PERIOD_LIMIT[$paymentMethod];

        $pastDateLimit = Carbon::now()->subDays($limit);

        if ($paymentDate->lessThan($pastDateLimit)) {
            return false;
        }

        return true;
    }

    private function markPaymentsRefunded(string $userId, string $reason, Refunded ...$refunds): void
    {
        foreach ($refunds as $refunded) {
            $this->markPaymentRefunded($userId, $reason, $refunded);
            $this->markPaymentPlanRefunded($userId, $reason, $refunded);
        }
    }

    private function markPaymentRefunded(string $userId, string $reason, Refunded $refunded)
    {
        $status = $refunded->refundResponse->getStatus();
        $payment = $refunded->payment;

        $refundStatusMapping = [
            Constants::PAGARME_TRANSACTION_PENDING_REFUND => Payment::STATUS_PENDING_REFUND,
        ];

        $payment->status = $refundStatusMapping[$status] ?? Payment::STATUS_REFUNDED;
        $payment->cancellation_reason = $reason;
        $payment->cancellation_at = Carbon::now();
        $payment->cancellation_user = $userId;
        $payment->save();
    }

    private function markPaymentPlanRefunded(string $userId, string $reason, Refunded $refunded)
    {
        $status = $refunded->refundResponse->getStatus();
        $payment = $refunded->payment;

        $refundStatusMapping = [
            Constants::PAGARME_TRANSACTION_PENDING_REFUND => PaymentPlan::STATUS_PENDING_REFUND,
            Constants::PAGARME_TRANSACTION_REFUNDED => PaymentPlan::STATUS_REFUNDED,
        ];

        $paymentPlans = $payment->plans();
        $paymentPlans->updateExistingPivot($paymentPlans->allRelatedIds(), [
            'status' => $refundStatusMapping[$status] ?? Payment::STATUS_REFUNDED,
        ]);
    }

    private function markPartialPaymentsRefunded(
        string $userId,
        string $paymentPlanId,
        string $reason,
        bool $shouldCancelPendingPayments,
        Refunded ...$refunds
    ): void {
        $orderNumber = array_first($refunds)->payment->order_number;
        $originalPlanId = PaymentPlan::find($paymentPlanId)->plan_id;

        foreach ($refunds as $refunded) {
            $paymentId = $refunded->payment->id;

            $paymentPlans = PaymentPlan::where('payment_id', $paymentId)->get();

            $isSingleProduct = $paymentPlans->groupBy('plan_id')->count() == 1;
            if ($isSingleProduct) {
                $this->markPaymentRefunded($userId, $reason, $refunded);
                if ($shouldCancelPendingPayments) {
                    $this->markPendingPaymentsAsCanceled($orderNumber);
                }
            }

            $paymentPlansRefunded = $paymentPlans->where('plan_id', $originalPlanId);
            foreach ($paymentPlansRefunded as $paymentPlan) {
                $this->markPartialPaymentPlanRefunded($userId, $paymentPlan->id, $reason, $refunded);
            }
        }

        if ($shouldCancelPendingPayments) {
            $this->markRemainingPaymentPlansAsCanceled($orderNumber, $originalPlanId);
        }
    }

    private function markPartialPaymentPlanRefunded(
        string $userId,
        string $paymentPlanId,
        string $reason,
        Refunded $refunded
    )
    {
        $status = $refunded->refundResponse->getStatus();

        $refundStatusMapping = [
            Constants::PAGARME_TRANSACTION_PENDING_REFUND => PaymentPlan::STATUS_PENDING_REFUND,
            Constants::PAGARME_TRANSACTION_REFUNDED => PaymentPlan::STATUS_REFUNDED,
        ];

        $finalStatus = $refundStatusMapping[$status] ?? Payment::STATUS_REFUNDED;

        $paymentPlans = PaymentPlan::query()->whereIn('id', [$paymentPlanId])->get();
        $paymentPlans->each(fn(PaymentPlan $paymentPlan) => $paymentPlan->update(['status' => $finalStatus]));
    }

    private function markPaymentsRefundedByStudent(string $reason, Refunded ...$refunds): void
    {
        foreach ($refunds as $refunded) {
            $this->markPaymentRefundedByStudent($reason, $refunded);
        }
    }

    private function markPaymentRefundedByStudent(string $reason, Refunded $refunded)
    {
        $status = $refunded->refundResponse->getStatus();
        $payment = $refunded->payment;

        $refundStatusMapping = [
            Constants::PAGARME_TRANSACTION_PENDING_REFUND => Payment::STATUS_PENDING_REFUND,
        ];

        $payment->status = $refundStatusMapping[$status] ?? Payment::STATUS_REFUNDED;
        $payment->cancellation_reason = $reason;
        $payment->cancellation_at = Carbon::now();
        $payment->cancellation_origin = 'subscriber';
        $payment->save();
    }

    private function sendRefundEmail(Payment $payment, ?RefundResponse $refunded = null, ?PaymentPlan $paymentPlan = null): void
    {
        try {
            $rawData = $refunded ? $refunded->getRawData() : null;

            $authorizationCode = $rawData['authorization_code'] ?? '';

            $subscriber = $payment->subscriber;

            if ($paymentPlan) {
                //partial refund
                $totalPlansValue = $price = $paymentPlan->plan_value;
            } else {
                //total refund
                $price = $payment->price;
                $totalPlansValue = $payment->getTotalPlansValue();
            }

            $mail = new SendMailRefund(
                $payment->platform_id,
                $subscriber,
                $payment,
                $authorizationCode,
                $price,
                $totalPlansValue,
                $paymentPlan
            );

            EmailService::mail([$subscriber->email], $mail);
        } catch (Exception  $e) {
            // ignore errors when sending email
        }
    }

    private function validatePlatformPayment(string $platformId, string $paymentId): void
    {
        $hasPayment = Payment::where('platform_id', $platformId)
            ->where('id', $paymentId)
            ->exists();

        if (!$hasPayment) {
            throw new InvalidPaymentException('Payment not valid for this platform');
        }
    }

    private function validateStudentPayment(array $subscriberIds, string $paymentId, string $token): void
    {

        $payment = Payment::query()
            ->where('id', $paymentId)
            ->whereIn('subscriber_id', $subscriberIds)
            ->first();

        if (!$payment) {
            throw new InvalidPaymentException('Payment not belongs to subscriber');
        }

        if ($payment['two_factor_code'] != $token) {
            $message = 'Two factor code not match: '.$token;
            throw new InvalidPaymentException($message);
        }

        $limitDate = Carbon::createFromFormat('Y-m-d H:i:s', $payment['two_factor_expires_at']);
        $currentDate = Carbon::now();

        if($currentDate->gt($limitDate))
            throw new InvalidPaymentException('Two factor date expirated.');


    }

    /**
     * Given a payment, list all related paid payments
     *
     * @param  string  $paymentId
     * @return \App\Repositories\Contracts\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Collection
     */
    private function listPaidPaymentsRelatedToPayment(string $paymentId)
    {
        $referencePayment = Payment::findOrFail($paymentId);

        $payments = $this->paymentRepository->baseFindWhere([
            'order_number' => $referencePayment->order_number,
            'status' => 'paid'
        ]);

        return $payments;
    }

    /**
     * @param  \App\Subscriber  $subscriber
     * @param  \App\Services\Finances\Refund\Objects\BankRefund  $bankData
     * @throws \App\Exceptions\Finances\InvalidPaymentException
     */
    private function validateSubscriberDocumentBank(Subscriber $subscriber, BankRefund $bankData): void
    {
        $subscriberDocument = $subscriber->document_number ?? '';
        if (!$subscriberDocument) {
            throw new InvalidPaymentException('Subscriber without document');
        }

        $givenDocument = $bankData->documentNumber ?? '';
        if (Formatter::onlyDigits($givenDocument) != Formatter::onlyDigits($subscriberDocument)) {
            throw new InvalidPaymentException('Invalid document');
        }
    }

    private function markPendingPaymentsAsCanceled($orderNumber): void
    {
        Payment::query()
            ->where('order_number', $orderNumber)
            ->where('status', Payment::STATUS_PENDING)
            ->update([
                'status' => Payment::STATUS_CANCELED
            ]);
    }

    private function markRemainingPaymentPlansAsCanceled(string $orderNumber, string $planId): void
    {
        $paymentIds = Payment::query()
            ->where('order_number', $orderNumber)
            ->get('id')
            ->toArray();

        $remainingPendingPaymentPlans = PaymentPlan::query()
            ->where('plan_id', $planId)
            ->where('status', PaymentPlan::STATUS_PENDING)
            ->whereIn('payment_id', $paymentIds)
            ->get();

        foreach ($remainingPendingPaymentPlans as $remainingPendingPaymentPlan) {
            $remainingPendingPaymentPlan->status = PaymentPlan::STATUS_CANCELED;
            $remainingPendingPaymentPlan->save();
        }
    }
}
