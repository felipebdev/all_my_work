<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'plan_id_integration', 'type_plan', 'trigger_email', 'description', 'message_success_checkout', 'image_id', 'approved', 'plan',
        'price', 'payment_method_boleto', 'payment_method_credit_card', 'installment', 'checkout_layout', 'checkout_address', 'platform_id',
        'status', 'currency', 'payment_method_free'
    ];

    const PLAN_TYPE_SUBSCRIPTION = 'R';
    const PLAN_TYPE_SALE = 'P';

    const PLAN_ITEM_CHARGE = "Cobrança";
    const PLAN_ITEM_REGISTRATION = "Matrícula";
    const ORDER_ITEM_CATEGORY_COURSE = "course";

    const FREE_DAYS_TYPE_FREE = 'free';
    const FREE_DAYS_TYPE_TRIAL = 'trial';

    /**
     * Get promotional price
     * @param int $parcelNumber
     * @return mixed
     */
    public function getPrice($parcelNumber = 1) {
        if( $this->use_promotional_price == true && $this->promotional_price > 0 && $parcelNumber <= $this->promotional_periods ) {
            return $this->promotional_price;
        }
        return $this->price;
    }

    static function allRecurrences()
    {
        return [
            "1" => "Única",
            "7" => "Semanal",
            "30" => "Mensal",
            "60" => "Bimestral",
            "90" => "Trimestral",
            "180" => "Semestral",
            "360" => "Anual",
        ];
    }

    static function allCurrencys()
    {
        return [
            "BRL" => "BRL",
            "EUR" => "EUR",
            "USD" => "USD",
        ];
    }

    static function allFreeDaysType()
    {
        return  [
            self::FREE_DAYS_TYPE_FREE => "Grátis",
            self::FREE_DAYS_TYPE_TRIAL => "Experiência"
        ];
    }

    static function getDescription($recurrence)
    {
        $recurrences = [
            "1" => "parcela única",
            "7" => "semanais",
            "30" => "mensais",
            "60" => "bimestrais",
            "90" => "trimestrais",
            "180" => "semestrais",
            "360" => "anuais",
        ];
        return $recurrences[$recurrence];
    }

    static function getBillingCycle($key)
    {
        $data = [
            "1" => 1,
            "7" => 52,
            "30" => 12,
            "60" => 6,
            "90" => 4,
            "180" => 2,
            "360" => 1
        ];

        return $data[$key];
    }

    static function getType($key)
    {
        $data = [
            "1" => "specific",
            "7" => "specific",
            "30" => "monthly",
            "60" => "bimonthly",
            "90" => "quarterly",
            "180" => "semesterly",
            "360" => "yearly"
        ];

        return $data[$key];
    }

    static function getIntervalMundi($key)
    {
        $data = [
            "1" => "day",
            "7" => "week",
            "30" => "month",
            "60" => "month",
            "90" => "month",
            "180" => "month",
            "360" => "year"
        ];

        return $data[$key];
    }

//    public function integrationType()
//    {
//        return $this->morphMany(IntegrationType::class, 'integratable');
//    }

    public function image()
    {
        return $this->hasOne(File::class, 'id', 'image_id');
    }

    public function integration()
    {
        return $this->morphOne(IntegrationType::class, 'integratable');
    }

    public function integratable()
    {
        return $this->morphMany(IntegrationType::class, 'integratable');
    }

    public function subscriber()
    {
        return $this->hasOne(Subscriber::class);
    }

    public function orderable()
    {
        return $this->morphMany('App\Order', 'orderable');
    }

    public function platform()
    {
        return $this->belongsTo(Platform::class, 'platform_id', 'id');
    }

    public function order_bump_image()
    {
        return $this->hasOne(File::class, 'id', 'order_bump_image_id');
    }

    public function upsell_image()
    {
        return $this->hasOne(File::class, 'id', 'upsell_image_id');
    }

    public static function getInstallmentValue($totalValue, $installment = 1)
    {
        if( $installment == 0 ) {
            $installment = 1;
        }
        if( $installment == 1 ) {
            return $totalValue;
        }
        // 2,923%
        $pmt = self::PMT(0.02923, $installment, $totalValue);
        return round($pmt, 2);
    }

    /**
     * Copy of Excel's PMT function.
     *
     * @param double $interest        The interest rate for the loan.
     * @param int    $num_of_payments The total number of payments for the loan in months.
     * @param double $PV              The present value, or the total amount that a series of future payments is worth now;
     *                                Also known as the principal.
     * @param double $FV              The future value, or a cash balance you want to attain after the last payment is made.
     *                                If fv is omitted, it is assumed to be 0 (zero), that is, the future value of a loan is 0.
     * @param int    $Type            Optional, defaults to 0. The number 0 (zero) or 1 and indicates when payments are due.
     *                                0 = At the end of period
     *                                1 = At the beginning of the period
     *
     * @return float
     */
    private static function PMT($interest,$num_of_payments,$PV,$FV = 0.00, $Type = 0) {
        $xp=pow((1+$interest),$num_of_payments);
        return
            ($PV* $interest*$xp/($xp-1)+$interest/($xp-1)*$FV)*
            ($Type==0 ? 1 : 1/($interest+1));
    }

}
