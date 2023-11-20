<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producer extends Model
{
    use HasFactory;

    public const TYPE_AFFILIATE = 'A';
    public const TYPE_PRODUCER = 'P';

    protected $fillable = [
        'id',
        'platform_id',
        'platform_user_id',
        'accepted_terms',
        'document_type',
        'document',
        'holder_name',
        'account_type',
        'bank',
        'branch',
        'account',
        'branch_check_digit',
        'account_check_digit',
        'document_verified',
        'recipient_id',
        'recipient_status',
        'recipient_reason',
        'recipient_pagarme',
        'recipient_gateway',
    ];

    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }

    public function platformUser()
    {
        return $this->belongsTo(PlatformUser::class);
    }

    public function producerProduct()
    {
        return $this->hasMany(ProducerProduct::class);
    }

    public function recurrences()
    {
        return $this->hasMany(Recurrence::class);
    }

}
