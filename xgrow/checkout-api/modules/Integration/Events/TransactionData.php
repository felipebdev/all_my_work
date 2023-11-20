<?php

namespace Modules\Integration\Events;

use App\Services\Finances\PaymentChange\ChangeCardUrlService;
use App\Transaction;

class TransactionData extends EventData
{
    /**
     * Payload attributes
     */
    private array $fillable = [
        'subscriber.id' => 'subscriber_id',
        'subscriber.name' => 'subscriber_name',
        'subscriber.email' => 'subscriber_email',
        'subscriber.cel_phone' => 'subscriber_phone',
        'subscriber.address_zipcode' => 'subscriber_zipcode',
        'subscriber.address_street' => 'subscriber_street',
        'subscriber.address_number' => 'subscriber_number',
        'subscriber.address_comp' => 'subscriber_comp',
        'subscriber.address_district' => 'subscriber_district',
        'subscriber.address_city' => 'subscriber_city',
        'subscriber.address_state' => 'subscriber_state',
        'subscriber.address_country' => 'subscriber_country',
        'subscriber.birthday' => 'subscriber_birthday',
        'subscriber.document_number' => 'subscriber_document_number',
        'subscriber.document_type' => 'subscriber_document_type',
        'subscriber.plan_id' => 'subscriber_plan_id',
        'subscriber.phone_country_code' => 'subscriber_phone_country_code',
        'subscriber.phone_area_code' => 'subscriber_phone_area_code',
        'subscriber.phone_number' => 'subscriber_phone_number',
        'transaction.id' => 'transaction_id',
        'transaction.platform_id' => 'transaction_platform_id',
        'transaction.order_code' => 'transaction_order_code',
        'transaction.status' => 'transaction_status',
        'transaction.type' => 'transaction_type',
        'transaction.transaction_code' => 'transaction_op_code',
        'transaction.transaction_message' => 'transaction_op_message',
        'transaction.total' => 'transaction_total',
        'transaction.origin' => 'transaction_origin',
        'plans' => 'transaction_plans',
        // "virtual" payment data
        'payment.order_code' => 'payment_order_code',
        'payment.price' => 'payment_price',
        'payment.status' => 'payment_status',
        'payment.type' => 'payment_type',
        'change_card_url' => 'change_card_url',
    ];

    private ChangeCardUrlService $changeCardUrlService;

    public function __construct(Transaction $transaction)
    {
        $this->changeCardUrlService = app()->make(ChangeCardUrlService::class);

        $subscriberAttributes = arrayAddPrefixKey('subscriber.', $transaction->subscriber->getAttributes());

        $transactionAttributes = arrayAddPrefixKey('transaction.', $transaction->getAttributes());

        $object = (object) array_merge(
            $subscriberAttributes,
            $transactionAttributes,
            ['plans' => $this->getPlans($transaction)],
            $this->getVirtualPaymentData($transaction),
            ['change_card_url' => $this->getChangeCardUrl($transaction)]
        );

        $attributes = parent::normalize($this->fillable, $object);
        parent::__construct($attributes);
    }

    private function getPlans(Transaction $transaction)
    {
        $plansAttributes = [];
        $plans = $transaction->plans;
        foreach ($plans as $plan) {
            $plansAttributes[] = [
                'id' => $plan->id,
                'plan' => $plan->name,
                'type' => $plan->pivot->type ?? '-',
                'price' => $plan->pivot->price
            ];
        }

        return $plansAttributes;
    }

    private function getVirtualPaymentData(Transaction $transaction): array
    {
        return [
            'payment.order_code' => $transaction->order_code,
            'payment.price' => $transaction->total,
            'payment.status' => $transaction->status,
            'payment.type' => $transaction->type,
        ];
    }

    private function getChangeCardUrl(Transaction $transaction): string
    {
        return $this->changeCardUrlService->generateUrlWithToken(
            $transaction->platform_id,
            $transaction->subscriber->email,
            $transaction->subscriber->document_number,
        );
    }

}
