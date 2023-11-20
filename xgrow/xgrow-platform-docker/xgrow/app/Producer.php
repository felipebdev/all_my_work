<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Producer extends Model
{

    public const TYPE_AFFILIATE = 'A';
    public const TYPE_PRODUCER = 'P';

    const MIN_COMMISSION_AFFILIATE = 1;
    const MAX_COMMISSION_AFFILIATE = 80;

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
        'recipient_gateway',
        'type',
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

}
