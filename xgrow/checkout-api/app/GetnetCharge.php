<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GetnetCharge extends Model
{
    protected $primary = 'charge_id';

    protected $fillable = [
        'charge_id', 'seller_id', 'subscription_id', 'customer_id', 'plan_id', 'payment_id','amount',
        'status','scheduled_date','create_date','retry_number','payment_date','payment_type',
        'terminal_nsu','authorization_code','acquirer_transaction_id','installment'
    ];




}
