<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{

    public const CART_STATUS_INITIATED = 'initiated';
    public const CART_STATUS_ABANDONED = 'abandoned';
    public const CART_STATUS_ORDERED = 'ordered';
    public const CART_STATUS_CONFIRMED = 'confirmed';
    public const CART_STATUS_DENIED = 'denied';

    public const TYPE_PRODUCT = 'product';
    public const TYPE_ORDER_BUMP = 'order_bump';
    public const TYPE_UPSELL = 'upsell';

    protected $fillable = [
        'name',
        'email',
        'cel_phone',
        'document_type',
        'document_number',
        'address_zipcode',
        'address_street',
        'address_number',
        'address_comp',
        'address_district',
        'address_city',
        'address_state',
        'address_country',
        'platform_id',
        'subscriber_id',
        'plan_id',
        'cart_status',
        'cart_status_updated_at',
        'type', // product, order_bump, upsell
        'payment_method',
    ];

    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }

    public function subscriber()
    {
        return $this->belongsTo(Subscriber::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }


}
