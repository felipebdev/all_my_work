<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentCards extends Model
{
    protected $fillable = [
        'platform_id', 'course_id', 'subscriber_id',
        'payment_id', 'seller_id', 'amount', 'currency',
        'order_id', 'status', 'received_at', 'credit_delayed',
        'credit_authorization_code', 'credit_authorized_at',
        'credit_reason_code', 'credit_reason_message',
        'credit_acquirer', 'credit_soft_descriptor', 'credit_brand',
        'credit_terminal_nsu', 'credit_acquirer_transaction_id', 'credit_transaction_id'
    ];

}
