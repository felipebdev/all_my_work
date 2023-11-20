<?php

namespace App;

use App\Platform;
use App\Services\Objects\PeriodFilter;
use App\Subscriber;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'subscription_id',
        'platform_id',
        'price',
        'payment_data',
        'status',
        'id_webhook',
        'type_payment',
        'payment_source',
        'customer_id',
        'subscriber_id',
        'installments',
        'antecipation_value',
        'expires_at',
        'cancellation_origin'
    ];

    protected $dates = [
        'confirmed_at',
        'created_at',
        'updated_at',
    ];

    const STATUS_PAID = 'paid';
    const STATUS_PENDING = 'pending';
    const STATUS_CANCELED = 'canceled';
    const STATUS_FAILED = 'failed';
    const STATUS_CHARGEBACK = 'chargeback';
    const STATUS_EXPIRED = 'expired';
    const STATUS_PENDING_REFUND = 'pending_refund';
    const STATUS_REFUNDED = 'refunded';

    public static function listStatus() {
        return array(
            self::STATUS_PAID => 'Pago',
            self::STATUS_PENDING => 'Pendente',
            self::STATUS_CANCELED => 'Cancelado',
            self::STATUS_FAILED => 'Falhou',
            self::STATUS_CHARGEBACK => 'Chargeback',
            self::STATUS_EXPIRED => 'Expirado',
            self::STATUS_PENDING_REFUND => 'Estorno pendente',
            self::STATUS_REFUNDED => 'Estornado',
        );
    }

    const TYPE_UNLIMITED ='U'; //Venda sem limite
    const TYPE_SALE ='P'; //Venda simples
    const TYPE_SUBSCRIPTION ='R'; //Assinatura

    const TYPE_PAYMENT_CREDIT_CARD = 'credit_card';
    const TYPE_PAYMENT_BILLET = 'boleto';
    const TYPE_PAYMENT_PIX = 'pix';

    public static function listTypePayments() {
        return [
            self::TYPE_PAYMENT_CREDIT_CARD => 'Cartão de Crédito',
            self::TYPE_PAYMENT_BILLET => 'Boleto',
            self::TYPE_PAYMENT_PIX => 'PIX',
        ];
    }

    const PAYMENT_SOURCE_CHECKOUT = 'C';
    const PAYMENT_SOURCE_LA = 'L';
    const PAYMENT_SOURCE_PLATFORM = 'P'; // manual payment via Platform
    const PAYMENT_SOURCE_AUTOMATIC = 'A';

    public static function listPaymentSources(?array $only = null)
    {
        $allPaymentSources = self::allPaymentSources();
        return is_null($only) ? $allPaymentSources : array_intersect($only, $allPaymentSources);
    }

    protected static function allPaymentSources()
    {
        return [
            self::PAYMENT_SOURCE_CHECKOUT => 'Checkout',
            self::PAYMENT_SOURCE_LA => 'Learning Area',
            self::PAYMENT_SOURCE_PLATFORM => 'Plataforma',
            self::PAYMENT_SOURCE_AUTOMATIC => 'Automática',
        ];
    }

    const PAYMENT_MULTIMEANS_TYPE_BOLETO = 'b'; // multi boleto [*]
    const PAYMENT_MULTIMEANS_TYPE_CARTAO = 'c'; // multiple cards
    const PAYMENT_MULTIMEANS_TYPE_PIX = 'p'; // multi pix [*]
    const PAYMENT_MULTIMEANS_TYPE_BOLETO_CARTAO = 'bc';
    const PAYMENT_MULTIMEANS_TYPE_BOLETO_PIX = 'bp'; // boleto + pix [*]
    const PAYMENT_MULTIMEANS_TYPE_CARTAO_PIX = 'cp';
    const PAYMENT_MULTIMEANS_TYPE_BOLETO_CARTAO_PIX = 'bcp';

    public function recurrences() {
        return $this->belongsToMany('App\Recurrence');
    }

    public function plans() {
        return $this->belongsToMany(Plan::class)
            ->withPivot(
                'tax_value', 'plan_value', 'plan_price', 'coupon_id', 'coupon_code',
                'coupon_value', 'type', 'customer_value'
            )
            ->withTimestamps();
    }

    public function subscription() {
        return $this->belongsTo(Subscription::class, 'order_number', 'order_number');
    }

    public function subscriber() {
        return $this->belongsTo(Subscriber::class);
    }

    public function platform() {
        return $this->belongsTo(Platform::class);
    }

    public function attendances()
    {
        return $this->bellongsTo(Attendance::class);
    }

    public function paymentPlans(){
        return $this->hasMany(PaymentPlan::class);
    }

    public function getTotalPlansValue() {
        $totalValue = 0;
        foreach($this->plans as $cod=>$plan) {
            $totalValue = $totalValue + $plan->price;
        }
        return $totalValue;
    }


    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('id', $value)
            ->where('platform_id', Auth::user()->platform_id)
            ->firstOrFail();
    }

    public function scopePlatform($query, string $platformId = null) {
        if (!empty($platformId)) {
            $query->where('payments.platform_id', '=', $platformId);
        }
        else {
            $query->where('payments.platform_id', '=', Auth::user()->platform_id);
        }

        return $query;
    }

    public function scopeOnPeriod($query, PeriodFilter $filter = null) {
        if ($filter instanceof PeriodFilter) {
            $query->whereBetween('payments.created_at', [
                $filter->startDate,
                $filter->endDate
            ]);
        }
        else {
            $query->whereDate(
                'payments.created_at',
                '<=',
                Carbon::today()->toDateString()
            );
        }

        return $query;
    }

    public function scopeStatus($query, string $status) {
        return $query->where('payments.status', $status);
    }
}
