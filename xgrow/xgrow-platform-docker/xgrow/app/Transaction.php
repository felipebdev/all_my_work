<?php

namespace App;

use App\Services\Objects\PeriodFilter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Transaction extends Model
{
    use SoftDeletes;

    const STATUS_SUCCESS = 'success';
    const STATUS_FAILED = 'failed';

    protected $table = 'transactions';
    public $timestamps = true;

    protected $fillable = [
        'platform_id',
        'subscriber_id',
        'status',
        'type',
        'order_code',
        'transaction_id',
        'transaction_code',
        'transaction_message',
        'total',
        'card_id',
        'payment_id'
    ];

    public function plans()
    {
        return $this->belongsToMany(Plan::class, 'transaction_plans', 'transaction_id', 'plan_id')
            ->withPivot('type', 'price');
    }

    public function subscriber()
    {
        return $this->belongsTo(Subscriber::class, 'subscriber_id', 'id');
    }

    public function scopePlatform($query, string $platformId = null) {
        if (!empty($platformId)) {
            $query->where('transactions.platform_id', '=', $platformId);
        }
        else {
            $query->where('transactions.platform_id', '=', Auth::user()->platform_id);
        }

        return $query;
    }

    public function scopeOnPeriod($query, PeriodFilter $filter = null) {
        if ($filter instanceof PeriodFilter) {
            $query->whereBetween('transactions.created_at', [
                $filter->startDate,
                $filter->endDate
            ]);
        }
        else {
            $query->whereDate(
                'transactions.created_at',
                '<=',
                Carbon::today()->toDateString()
            );
        }

        return $query;
    }
}
