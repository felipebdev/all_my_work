<?php

namespace Modules\Integration\Events;

use App\Lead;

class NewLeadData extends EventData
{
    /**
     * Payload attributes
     * @var array
     */
    private $fillable = [
        'id' => 'subscriber_id',
        'name' => 'subscriber_name',
        'email' => 'subscriber_email',
        'cel_phone' => 'subscriber_phone',
        'address_zipcode' => 'subscriber_zipcode',
        'address_street' => 'subscriber_street',
        'address_number' => 'subscriber_number',
        'address_comp' => 'subscriber_comp',
        'address_district' => 'subscriber_district',
        'address_city' => 'subscriber_city',
        'address_state' => 'subscriber_state',
        'address_country' => 'subscriber_country',
        'birthday' => 'subscriber_birthday',
        'document_number' => 'subscriber_document_number',
        'document_type' => 'subscriber_document_type',
        'plan_id' => 'subscriber_plan_id', // @deprecate this entry, use plan.id instead
        'phone_country_code' => 'subscriber_phone_country_code',
        'phone_area_code' => 'subscriber_phone_area_code',
        'phone_number' => 'subscriber_phone_number',
        'plan' => 'plan',
        'product' => 'product'
    ];

    public function __construct(Lead $lead)
    {
        $object = (object) array_merge(
            $lead->getAttributes(),
            ['plan' => $this->getPlan($lead)],
            ['product' => $this->getProduct($lead)],
        );

        $attributes = parent::normalize($this->fillable, $object);
        parent::__construct($attributes);
    }

    private function getPlan(Lead $lead): array
    {
        $plan = $lead->plan;

        $price = $plan->pivot->plan_value ?? $plan->price;
        $total = $plan->pivot->plan_price ?? $plan->price;

        return [
            'id' => $plan->id,
            'plan' => $plan->name,
            'type' => $plan->pivot->type ?? '-',
            'price' => $price,
            'price_plus_fees' => $total,
        ];
    }

    private function getProduct(Lead $lead): array
    {
        $product = $lead->plan->product;

        return [
            'id' => $product->id,
            'product' => $product->name,
            'type' => $product->type ?? '-',
            'description' => $product->description ?? '-',
            'support_email' => $product->support_email,
        ];
    }
}
