<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OneClick extends Model
{
    use HasFactory;

    public $table = 'one_click';

    public $keyType = 'string';

    public $incrementing = false;

    public $fillable = [
        'id',
        'platform_id',
        'subscriber_id',
        'payment_method',
        'installments',
        'previous_id',
        'expires_at',
        'locked_at',
        'tries',
        'used',
    ];

    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }

    public function subscriber()
    {
        return $this->belongsTo(Subscriber::class);
    }
}
