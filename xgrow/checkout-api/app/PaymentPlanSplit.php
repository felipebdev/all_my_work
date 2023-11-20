<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentPlanSplit extends Model
{
    protected $table = 'payment_plan_split';

    protected $fillable = [
        'id',
        'created_at',
        'updated_at',
        'client_id',
        'platform_id',
        'product_id',
        'order_code',
        'plan_id',
        'payment_plan_id',
        'producer_product_id',
        'percent',
        'antecipation_value',
        'type',
        'value'
    ];

    const SPLIT_TYPE_XGROW = 'X';
    const SPLIT_TYPE_CLIENT = 'C';
    const SPLIT_TYPE_PRODUCER = 'P';
    const SPLIT_TYPE_AFFILIATE = 'A';

    public static function listSplitTypes(?array $only = null)
    {
        $allTypes = self::allSplitTypes();
        return is_null($only) ? $allTypes : array_intersect($only, $allTypes);
    }

    protected function allSplitTypes()
    {
        return [
            self::SPLIT_TYPE_XGROW => 'Xgrow',
            self::SPLIT_TYPE_CLIENT => 'Produtor',
            self::SPLIT_TYPE_PRODUCER => 'Coprodutor',
            self::SPLIT_TYPE_AFFILIATE => 'Afiliado',
        ];
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function producerProduct()
    {
        return $this->belongsTo(ProducerProduct::class);
    }
}
