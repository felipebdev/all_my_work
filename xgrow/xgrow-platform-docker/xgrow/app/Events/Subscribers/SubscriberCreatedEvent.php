<?php

namespace App\Events\Subscribers;

use App\Platform;
use App\Subscriber;
use App\Events\BaseEvent;

/**
 * @deprecated v0.23
 */
class SubscriberCreatedEvent extends BaseEvent
{   
    public $queue = 'xgrow-events:subscriber-OnCreate:';
    public $trigger = 'OnCreateSubscriber';
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
        'phone_country_code' => 'subscriber_phone_country_code',
        'phone_area_code' => 'subscriber_phone_area_code',
        'phone_number' => 'subscriber_phone_number',
        'plan_id' => 'subscriber_plan'
    ];

    public function __construct(Platform $platform, Subscriber $subscriber) {
        $metadata = parent::normalize($this->fillable, $subscriber);
        parent::__construct($platform, $metadata);
    }
}
