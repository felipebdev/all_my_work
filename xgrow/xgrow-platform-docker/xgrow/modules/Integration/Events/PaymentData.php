<?php

namespace Modules\Integration\Events;

use App\Payment;

class PaymentData extends EventData
{
    const ORDER_BUMP_TYPE = 'order_bump';

    /**
     * Payload attributes
     * @var array
     */
    private $fillable = [
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
        'phone_country_code' => 'subscriber_phone_country_code',
        'phone_area_code' => 'subscriber_phone_area_code',
        'phone_number' => 'subscriber_phone_number',
        'payment.type' => 'payment_model',
        'payment.type_payment' => 'payment_type',
        'payment.payment_date' => 'payment_date',
        'payment.price' => 'payment_price',
        'payment.plans_value' => 'payment_plans_value',
        'payment.customer_value' => 'payment_customer_value',
        //'payment.service_value' => 'payment_service_value',
        //'payment.tax_value' => 'payment_tax_value',
        //'payment.antecipation_value' => 'payment_antecipation_value',
        'payment.installments' => 'payment_installments',
        'payment.installment_number' => 'payment_installment_number',
        'payment.intallments' => 'payment_installments',
        'payment.order_code' => 'payment_order_code',
        'payment.status' => 'payment_status',
        'plans' => 'payment_plans',
        'client.type_person' => 'client_type_person',
        'client.cpf' => 'client_cpf',
        'client.cnpj' => 'client_cnpj',
        'client.fantasy_name' => 'client_fantasy_name',
        'client.company_name' => 'client_company_name',
        'client.address' => 'client_address',
        'client.number' => 'client_number',
        'client.complement' => 'client_complement',
        'client.district' => 'client_district',
        'client.city' => 'client_city',
        'client.state' => 'client_state',
        'client.zipcode' => 'client_zipcode'
    ];

    public function __construct(Payment $payment, array $overrideData = [])
    {
        $subscriberAttributes = arrayAddPrefixKey('subscriber.', $payment->subscriber->getAttributes());
        $paymentAttributes = arrayAddPrefixKey('payment.', $payment->getAttributes());
        $clientAttributes = arrayAddPrefixKey('client.', $payment->platform->client->getAttributes());
        $plansAttributes = $this->getPlans($payment);
        $object = (object) array_merge(
            $clientAttributes, 
            $subscriberAttributes, 
            $paymentAttributes, 
            ['plans' => $plansAttributes],
            $overrideData
        );

        $attributes = parent::normalize($this->fillable, $object);
        parent::__construct($attributes);
    }

    private function getPlans($payment) {
        $plansAttributes = [];
        $plans = $payment->plans;
        $installments = (!empty($payment->installments)) ? $payment->installments : 1;
        $isNolimit = ($payment->type === Payment::TYPE_UNLIMITED);
        foreach ($plans as $plan) {
            $isOrderBump = ($plan->pivot->type === self::ORDER_BUMP_TYPE);
            $price = (is_null($plan->pivot->plan_value) ?
                $plan->price :
                ($isNolimit && $isOrderBump ? 
                    $plan->pivot->plan_value / $installments :
                    $plan->pivot->plan_value
                )
            );

            $total = (is_null($plan->pivot->plan_price) ?
                $plan->price :
                ($isNolimit && $isOrderBump ? 
                    $plan->pivot->plan_price / $installments :
                    $plan->pivot->plan_price
                )
            );

            $plansAttributes[] = [
                //'id' => $plan->id,
                'plan' => $plan->name,
                'type' => $plan->pivot->type ?? '-',
                'price' => $price,
                'price_plus_fees' => $total,
            ];
        }

        return $plansAttributes;
    }
}
