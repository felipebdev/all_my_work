<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    protected $fillable = [
        'id',
        'bank',
        'branch',
        'account',
        'created_at',
        'updated_at',
        'name',
        'email',
        'document',
        'recipient_id',
        'recipient_pagarme',
    ];
}
