<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Subscription extends Model
{

    protected $fillable = [
        'status',
        'status_updated_at',
        'canceled_at',
        'cancellation_reason'
    ];

    protected $dates = [
        'status_updated_at',
    ];

    const STATUS_ACTIVE = 'active';
    const STATUS_CANCELED = 'canceled';
    const STATUS_PENDING= 'pending';
    const STATUS_PENDING_PAYMENT = 'pending_payment';
    const STATUS_FAILED = 'failed';

    public static function listStatus()
    {
        return [
            self::STATUS_ACTIVE => 'Ativo',
            self::STATUS_CANCELED => 'Cancelado',
            self::STATUS_PENDING => 'Pendente',
            self::STATUS_PENDING_PAYMENT => 'Pagamento pendente',
            self::STATUS_FAILED => 'Falha no pagamento',
        ];
    }

//    public function integration()
//    {
//        return $this->morphMany(IntegrationType::class, 'integratable');
//    }

    public function integratable()
    {
        return $this->morphMany(IntegrationType::class, 'integratable');
    }

    public function plan(){
        return $this->belongsTo(Plan::class);
    }

    public function transaction() {
        return $this->hasOne(Payment::class, 'order_code', 'gateway_transaction_id');
    }

    public function payments() {
        return $this->hasMany(Payment::class, 'order_number', 'order_number');
    }

    public function subscriber() {
        return $this->belongsTo(Subscriber::class);
    }

    public function orderable()
    {
        return $this->morphMany('App\Order', 'orderable');
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
            $query->where('subscriptions.platform_id', '=', $platformId);
        }
        else {
            $query->where('subscriptions.platform_id', '=', Auth::user()->platform_id);
        }

        return $query;
    }
}
