<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $fillable = ['subscription_id', 'platform_id', 'price', 'payment_date', 'status', 'id_webhook', 'type_payment', 'customer_id', 'subscriber_id', 'installments'];

    const STATUS_PAID = 'paid';
    const STATUS_PENDING = 'pending';
    const STATUS_CANCELED = 'canceled';
    const STATUS_REFUNDED = 'refunded';
    const STATUS_CHARGEBACK = 'chargeback';
    const STATUS_EXPIRED = 'expired';


    public static function listStatus() {
        return array(
            self::STATUS_PAID => 'Pago',
            self::STATUS_PENDING => 'Pendente',
            self::STATUS_CANCELED => 'Cancelado',
            self::STATUS_REFUNDED => 'Refunded',
            self::STATUS_CHARGEBACK => 'Chargeback',
            self::STATUS_EXPIRED => 'Expirado',
        );
    }

    const TYPE_UNLIMITED ='U'; //Venda sem limite
    const TYPE_SALE ='P'; //Venda simples
    const TYPE_SUBSCRIPTION ='R'; //Assinatura

    const TYPE_PAYMENT_CREDIT_CARD = 'credit_card';
    const TYPE_PAYMENT_BILLET = 'boleto';

    public static function listTypePayments() {
        return array(
            self::TYPE_PAYMENT_CREDIT_CARD => 'Cartão de Crédito',
            self::TYPE_PAYMENT_BILLET => 'Boleto',
        );
    }

    public function recurrences() {
        return $this->belongsToMany('App\Recurrence');
    }

    public function plans() {
        return $this->belongsToMany(Plan::class)->withTimestamps();
    }
}
