<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Integration extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name_integration', 'source_token', 'flag_enable', 'days_limit_payment_pendent', 'trigger_email', 'id'
    ];

    const STATUS_CANCELED = 'canceled';

    static function integrations()
    {
        return [
          1 => ['name' => 'Hotmart', 'status' => 1],
          2 => ['name' => 'Superlogica', 'status' => 0],
          3 => ['name' => 'Billsby', 'status' => 0],
          4 => ['name' => 'GetNet', 'status' => 0],
          5 => ['name' => 'Fandone', 'status' => 1],
          6 => ['name' => 'Eduzz', 'status' => 1],
          7 => ['name' => 'PLX', 'status' => 1],
          8 => ['name' => 'Active Campaign', 'status' => 1],
        ];

    }

    static function hotmartPurchaseStatus($status)
    {
        $subscriberStatus = [
            'chargeback' => 'canceled',
            'refunded' => 'canceled',
            'expired' => 'canceled',
            'dispute' => 'canceled',
            'delayed' => 'canceled',
            'completed' => 'canceled',
            'canceled' => 'canceled',
            'billet_printed' => 'trial',
            'approved' => 'active',
        ];

        return $subscriberStatus[$status];
    }

    static function hotmartSubscriptionStatus($status)
    {
        $subscriberStatus = [
            'active' => 'active',
            'canceled' => 'canceled',
            'past_due' => 'canceled',
            'expired' => 'canceled',
            'started' => 'active',
            'inactive' => 'canceled'
        ];

        return $subscriberStatus[$status];
    }

    static function eduzzPurchaseStatus($status)
    {
        $subscriberStatus = [
            1 => 'active',
            2 => 'active',
            3 => 'canceled',
            4 => 'canceled',
            7 => 'canceled',
            9 => 'active',
            10 => 'trial'
        ];

        return $subscriberStatus[$status];
    }

    static function eduzzRecurrency($interval)
    {
        $data = [
            'day' => 1,
            'week' => 7,
            'month' => 30,
            'year' => 365
        ];
        return $data[$interval];
    }

    static function eduzzInvoiceStatus($status)
    {
        $invoiceStatus = [
            1 => 'canceled',
            3 => 'active',
            4 => 'canceled',
            6 => 'canceled',
            7 => 'canceled',
            9 => 'canceled',
            10 => 'canceled',
            11 => 'canceled',
            15 => 'canceled'
        ];

        return $invoiceStatus[$status];
    }

    static function eduzzInvoiceStatusFinal($status)
    {
        $invoiceStatus = [
            1 => 'Aberta',
            3 => 'Paga',
            4 => 'Cancelada',
            6 => 'Aguardando reembolso',
            7 => 'Reembolsado',
            9 => 'Duplicada',
            10 => 'Expirada',
            11 => 'Em recuperação',
            15 => 'Aguardando pagamento'
        ];

        return $invoiceStatus[$status];
    }

    static function PLXPurchaseStatus($status)
    {
        $subscriberStatus = [
            1 => 'active',
            2 => 'active',
            3 => 'canceled',
            4 => 'canceled',
            7 => 'canceled',
            9 => 'active',
            10 => 'trial'
        ];

        return $subscriberStatus[$status];
    }

    static function PLXRecurrency($interval)
    {
        $data = [
            'day' => 1,
            'week' => 7,
            'month' => 30,
            'year' => 365
        ];
        return $data[$interval];
    }

    static function PLXInvoiceStatus($status)
    {
        $invoiceStatus = [
            1 => 'canceled',
            3 => 'active',
            4 => 'canceled',
            6 => 'canceled',
            7 => 'canceled',
            9 => 'canceled',
            10 => 'canceled',
            11 => 'canceled',
            15 => 'canceled'
        ];

        return $invoiceStatus[$status];
    }

    static function PLXInvoiceStatusFinal($status)
    {
        $invoiceStatus = [
            1 => 'Aberta',
            3 => 'Paga',
            4 => 'Cancelada',
            6 => 'Aguardando reembolso',
            7 => 'Reembolsado',
            9 => 'Duplicada',
            10 => 'Expirada',
            11 => 'Em recuperação',
            15 => 'Aguardando pagamento'
        ];

        return $invoiceStatus[$status];
    }

    public function integrationType()
    {
        return $this->belongsTo(IntegrationType::class, 'integration_id');
    }

}
