<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Recurrence extends Model
{
    protected $fillable = [
        'id',
        'subscriber_id',
        'recurrence',
        'last_invoice',
        'last_payment',
        'card_id',
        'current_charge',
        'created_at',
        'updated_at',
        'type',
        'payment_method',
        'total_charges',
        'plan_id',
        'order_number',
    ];

    const TYPE_SUBSCRIPTION = 'S';

    const PAYMENT_METHOD_CREDIT_CARD = 'credit_card';
    const PAYMENT_METHOD_BOLETO = 'boleto';
    const PAYMENT_METHOD_PIX = 'pix';

    public function subscriber() {
        return $this->belongsTo('App\Subscriber');
    }

    public function payments() {
        return $this->belongsToMany('App\Payment');
    }

    public function plan() {
        return $this->belongsTo(Plan::class);
    }

    public function affiliate()
    {
        return $this->belongsTo(Producer::class);
    }

}
