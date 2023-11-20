<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentMethodChange extends Model
{
    //use HasFactory;

    public $table = 'payment_method_change';

    public const ORIGIN_SUBSCRIBER = 'subscriber';
    public const ORIGIN_PRODUCER = 'producer';

    public $fillable = [
        'payment_id',
        'origin',
        'type_payment_old',
        'type_payment_new',
        'installments_old',
        'installments_new',
        'order_code_old',
        'order_code_new',
        'charge_id_old',
        'charge_id_new',
        'charge_code_old',
        'charge_code_new',
        'boleto_line_old',
        'boleto_line_new',
        'pix_qrcode_url_old',
        'pix_qrcode_url_new',
    ];
}
