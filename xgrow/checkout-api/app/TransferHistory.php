<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferHistory extends Model
{
    use HasFactory;

    public $timestamps = false;

    public $fillable = [
        // 'id',
        //'event_at',
        'platform_id',
        'user_id',
        'recipient_id',
        'amount',
        'status',
    ];

    public const STATUS_PROCESSING = 'processing';
    public const STATUS_SUCCESSFUL = 'successful';
    public const STATUS_REFUSED = 'refused';
    public const STATUS_FAILED = 'failed';

    public function platform()
    {
        return $this->belongsTo(Platform::class, 'platform_id', 'id');
    }

    public function platformUser()
    {
        return $this->belongsTo(PlatformUser::class);
    }

}
