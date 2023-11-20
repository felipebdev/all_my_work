<?php

namespace Modules\Integration\Events;

use App\Client;
use App\Payment;
use App\PaymentPlanSplit;
use Modules\Integration\Enums\EventEnum;

class PaymentData extends EventData
{
    const ORDER_BUMP_TYPE = 'order_bump';

    private Payment $payment;

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
        'payment.expires_at' => 'payment_expires_at',
        'payment.pix_qrcode' => 'payment_pix_qrcode',
        'plans' => 'payment_plans',
        'client.type_person' => 'client_type_person',
        'client.holder_name' => 'client_holder_name',
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

    public function __construct(Payment $payment, array $overrideData = [], ?string $event = null)
    {
        $this->payment = $payment;

        $subscriberAttributes = arrayAddPrefixKey('subscriber.', $payment->subscriber->getAttributes());
        $paymentAttributes = arrayAddPrefixKey('payment.', $payment->getAttributes());

        if ($payment->multiple_means && $event === EventEnum::PAYMENT_APPROVED) {
            // this is ugly as hell, but it's the easiest way to get total values on payment with multiple means
            $payments = Payment::where('order_code', $payment->order_code)->get();
            $paymentAttributes['payment.price'] = $payments->sum('price');
            $paymentAttributes['payment.customer_value'] = $payments->sum('customer_value');
            $paymentAttributes['payment.plans_value'] = $payments->sum('plans_value');
        }

        $clientAttributes = arrayAddPrefixKey('client.', $payment->platform->client->getAttributes());
        $plansAttributes = $this->getPlans($payment, $event);
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

    private function getPlans($payment, ?string $event = null)
    {
        $plansAttributes = [];
        $plans = $payment->plans;
        foreach ($plans as $plan) {
            $price = $plan->pivot->plan_value; // ?? $plan->price; // legacy
            $total = $plan->pivot->plan_price; // ?? $plan->price; // legacy

            if ($payment->multiple_means && $event === EventEnum::PAYMENT_APPROVED) {
                $payments = Payment::where('order_code', $payment->order_code)->get();

                $price = $payments->sum('plans_value');
                $total = $payments->sum('price');
            }

            $plansAttributes[] = [
                'id' => $plan->id,
                'plan' => $plan->name,
                'type' => $plan->pivot->type ?? '-',
                'price' => $price,
                'price_plus_fees' => $total,
                'coproducers' => $this->getCoproducers($payment, $plan),
            ];
        }

        return $plansAttributes;
    }

    public function getPayment(): Payment
    {
        return $this->payment;
    }

    private function getCoproducers($payment, $plan): array
    {
        $producerSplits = PaymentPlanSplit::query()
            ->where('order_code', $payment->order_code)
            ->where('plan_id', $plan->id)
            ->where('type', PaymentPlanSplit::SPLIT_TYPE_PRODUCER)
            ->get();


        if ($producerSplits->isEmpty()) {
            return [];
        }

        $producerProduct = $producerSplits->first()->producerProduct;
        $producer = $producerProduct->producer;
        $platformUser = $producer->platformUser;
        $client = Client::where('email', $platformUser->email)->first();

        $coproducerInfo = [
            'name' => $producer->document_type == 'cnpj'
                ? ($client->company_name ?? null)
                : ($producer->holder_name ?? null),
            'issue_invoice' => $producerProduct->split_invoice == 1,
            'invoice_percent' => $producerProduct->percent,
            'address' => $client->address ?? null,
            'city' => $client->city ?? null,
            'document' => $producer->document ?? null,
            'document_type' => $producer->document_type ?? null,
            'complement' => $client->complement ?? null,
            'district' => $client->district ?? null,
            'fantasy_name' => $client->fantasy_name ?? null,
            'number' => $client->number ?? null,
            'state' => $client->state ?? null,
            'type_person' => $client->type_person ?? null,
            'zipcode' => $client->zipcode ?? null,
        ];

        $coproducers = [];
        $coproducers[] = $coproducerInfo;

        return $coproducers;
    }

}
