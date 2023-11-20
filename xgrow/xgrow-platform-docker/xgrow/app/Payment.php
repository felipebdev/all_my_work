<?php

namespace App;

use App\Services\Objects\PeriodFilter;
use App\Services\Reports\FinancialSaleReportService;
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
    ];

    const STATUS_PAID = 'paid';
    const STATUS_PENDING = 'pending';
    const STATUS_CANCELED = 'canceled';
    const STATUS_FAILED = 'failed';
    const STATUS_CHARGEBACK = 'chargeback';
    const STATUS_EXPIRED = 'expired';
    const STATUS_REFUNDED = 'refunded';
    const STATUS_PENDING_REFUND = 'pending_refund';

    public static function listStatus()
    {
        return [
            self::STATUS_PAID => 'Pago',
            self::STATUS_PENDING => 'Pendente',
            self::STATUS_CANCELED => 'Cancelado',
            self::STATUS_FAILED => 'Falha no pagamento',
            self::STATUS_CHARGEBACK => 'Chargeback',
            self::STATUS_EXPIRED => 'Expirado',
            self::STATUS_REFUNDED => 'Estornado',
            self::STATUS_PENDING_REFUND => 'Estorno pendente'
        ];
    }

    const TYPE_UNLIMITED = 'U'; //Venda sem limite
    const TYPE_SALE = 'P'; //Venda simples
    const TYPE_SUBSCRIPTION = 'R'; //Assinatura

    public static function listTypes()
    {
        return [
            self::TYPE_SALE => 'Venda única',
            self::TYPE_UNLIMITED => 'Venda Sem limite',
            self::TYPE_SUBSCRIPTION => 'Assinatura',
        ];
    }

    const TYPE_PAYMENT_CREDIT_CARD = 'credit_card';
    const TYPE_PAYMENT_BILLET = 'boleto';
    const TYPE_PAYMENT_PIX = 'pix';

    public static function listTypePayments()
    {
        return [
            self::TYPE_PAYMENT_CREDIT_CARD => 'Cartão de Crédito',
            self::TYPE_PAYMENT_BILLET => 'Boleto',
            self::TYPE_PAYMENT_PIX => 'PIX',
        ];
    }

    const PAYMENT_SOURCE_CHECKOUT = 'C';
    const PAYMENT_SOURCE_LA = 'L';
    const PAYMENT_SOURCE_AUTOMATIC = 'A';
    const PAYMENT_SOURCE_ONE_CLICK = 'O';

    public static function listPaymentSources(?array $only = null)
    {
        $allPaymentSources = self::allPaymentSources();
        return is_null($only) ? $allPaymentSources : array_intersect($only, $allPaymentSources);
    }

    protected static function allPaymentSources()
    {
        return [
            self::PAYMENT_SOURCE_CHECKOUT => 'Checkout',
            self::PAYMENT_SOURCE_LA => 'Área de Aprendizado',
            self::PAYMENT_SOURCE_AUTOMATIC => 'Automática',
            self::PAYMENT_SOURCE_ONE_CLICK => 'One Click Buy',
        ];
    }

    public function recurrences()
    {
        return $this->belongsToMany('App\Recurrence');
    }

    public function plans()
    {
        return $this->belongsToMany(Plan::class)
            ->withPivot(
                'tax_value',
                'plan_value',
                'plan_price',
                'coupon_id',
                'coupon_code',
                'coupon_value',
                'type',
                'customer_value'
            )
            ->withTimestamps();
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class, 'order_number', 'order_number');
    }

    public function subscriber()
    {
        return $this->belongsTo(Subscriber::class);
    }

    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }

    public function attendances()
    {
        return $this->bellongsTo(Attendance::class);
    }

    public function getTotalPlansValue()
    {
        $totalValue = 0;
        foreach ($this->plans as $cod => $plan) {
            $totalValue = $totalValue + $plan->price;
        }
        return $totalValue;
    }

    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value)
    {
        return $this->where('id', $value)
            ->where('platform_id', Auth::user()->platform_id)
            ->firstOrFail();
    }

    public function scopePlatform($query, string $platformId = null)
    {
        if (!empty($platformId)) {
            $query->where('payments.platform_id', '=', $platformId);
        } else {
            $query->where('payments.platform_id', '=', Auth::user()->platform_id);
        }

        return $query;
    }

    public function scopeOnPeriod($query, PeriodFilter $filter = null)
    {
        if ($filter instanceof PeriodFilter) {
            $query->whereBetween('payments.created_at', [
                $filter->startDate,
                $filter->endDate
            ]);
        } else {
            $query->whereDate(
                'payments.created_at',
                '<=',
                Carbon::today()->toDateString()
            );
        }
    }

    /**
     * Scope to filter by 'payment_date'
     *
     * @param $query
     * @param  \App\Services\Objects\PeriodFilter|null  $filter
     */
    public function scopeOnPaymentDatePeriod($query, PeriodFilter $filter = null)
    {
        if ($filter instanceof PeriodFilter) {
            $query->whereBetween('payments.payment_date', [
                $filter->startDate,
                $filter->endDate
            ]);
        } else {
            $query->whereDate(
                'payments.payment_date',
                '<=',
                Carbon::today()->toDateString()
            );
        }
    }

    /**
     * @param $query
     * @param PeriodFilter|null $filter
     * @return mixed
     */
    public function scopeOnSalesByStatus($query, PeriodFilter $filter = null)
    {
        return collect($this->getApiReport($filter))->get('response')->sale_by_status;
    }

    /**
     * @param $query
     * @param PeriodFilter|null $filter
     * @return array[]
     */
    public function scopeOnTransactionsPeriod($query, PeriodFilter $filter = null): array
    {
        $response = collect($this->getApiReport($filter))->get('response')->number_of_transactions;

        return [
            [
                "status" => "paid",
                "count" => $response->paid
            ],
            [
                "status" => "pending",
                "count" => $response->pending
            ],
            [
                "status" => "failed",
                "count" => $response->failed
            ],
            [
                "status" => "chargeback",
                "count" => $response->chargeback
            ],
            [
                "status" => "expired",
                "count" => $response->expired
            ],
            [
                "status" => "refunded",
                "count" => $response->refunded
            ],
            [
                "status" => "pending_refund",
                "count" => $response->pending_refund
            ],
            [
                "status" => "total",
                "count" => $response->total
            ]
        ];
    }

    public function scopeStatus($query, string $status)
    {
        return $query->where('payments.status', $status);
    }

    /**
     * @param PeriodFilter|null $filter
     * @return mixed|null
     */
    public function getApiReport(PeriodFilter $filter = null)
    {
        $queryString = 'period=' . formatDateAndTime($filter->startDate) . '-' . formatDateAndTime($filter->endDate);

        $url = "financial/transaction-summary?$queryString";

        $financialSaleReportService = new FinancialSaleReportService();

        return $financialSaleReportService->callFinancialApi($url);
    }
}
