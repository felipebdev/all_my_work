<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'attendant_id','subscriber_id', 'payment_id', 'audience_id', 'status', 'source_email'
    ];

    const STATUS_PENDING = "pending";
    const STATUS_GAIN = "gain";
    const STATUS_LOST = "lost";
    const STATUS_CONTACTLESS = "contactless";
    const STATUS_EXPIRED = "expired";

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('queue', function (Builder $builder) {
            $builder->where('queue', false);
        });
    }

    static function allStatus(){
        return $status = [
            self::STATUS_PENDING => 'Pendente',
            self::STATUS_GAIN => 'Ganho',
            self::STATUS_LOST => 'Perdido',
            self::STATUS_CONTACTLESS => 'Sem contato',
            self::STATUS_EXPIRED => 'Expirado'
        ];
    }

    public function attendant(){
    	return $this->belongsTo(Attendant::class);
    }

    public function subscriber(){
    	return $this->belongsTo(Subscriber::class);
    }

    public function payments()
    {
        return $this->belongsTo(Payment::class);
    }

    public function audience(){
    	return $this->belongsTo(Audience::class);
    }

    public function contacts(){
    	return $this->hasMany(AttendanceContact::class);
    }

}
