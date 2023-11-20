<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProducerProduct extends Model
{

    const STATUS_PENDING = 'pending';
    const STATUS_ACTIVE = 'active';
    const STATUS_CANCELED = 'canceled';

    public static function listStatus(?array $only = null)
    {
        $allStatus = self::allStatus();
        return is_null($only) ? $allStatus : array_intersect($only, $allStatus);
    }

    protected static function allStatus()
    {
        return [
            self::STATUS_ACTIVE => 'Ativo',
            self::STATUS_CANCELED => 'Cancelado',
            self::STATUS_PENDING => 'Pendente',
        ];
    }

    protected $fillable = [
        'id',
        'producer_id',
        'product_id',
        'contract_limit',
        'percent',
        'status',
        'canceled_at',
    ];

    public function producer()
    {
        return $this->belongsTo(Producer::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

}
