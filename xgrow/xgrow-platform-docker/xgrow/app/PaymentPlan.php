<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class PaymentPlan extends Model
{
    //use HasFactory;

    protected $table = 'payment_plan';

    protected $fillable = [
        'id',
        'payment_id',
        'plan_id',
        'status',
        'tax_value',
        'plan_value',
        'plan_price',
        'coupon_id',
        'coupon_code',
        'coupon_value',
        'type',
        'customer_value',
    ];

    public const STATUS_PAID = 'paid';
    public const STATUS_PENDING = 'pending';
    public const STATUS_CANCELED = 'canceled';
    public const STATUS_FAILED = 'failed';
    public const STATUS_CHARGEBACK = 'chargeback';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_PENDING_REFUND = 'pending_refund';
    public const STATUS_REFUNDED = 'refunded';

    public static function listStatus(): array
    {
        return [
            self::STATUS_PAID => 'Pago',
            self::STATUS_PENDING => 'Pendente',
            self::STATUS_CANCELED => 'Cancelado',
            self::STATUS_FAILED => 'Falhou',
            self::STATUS_CHARGEBACK => 'Chargeback',
            self::STATUS_EXPIRED => 'Expirado',
            self::STATUS_PENDING_REFUND => 'Estorno pendente',
            self::STATUS_REFUNDED => 'Estornado',
        ];
    }

    protected function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    protected function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Get plan status data
     * @param $paymentId
     * @param $planId
     * @return Model|Builder|object|null
     */
    public static function getPaymentPlantData($paymentId, $planId)
    {
        return DB::table('payment_plan')
            ->where('payment_id', $paymentId)
            ->where('plan_id', $planId)
            ->first();
    }
}
