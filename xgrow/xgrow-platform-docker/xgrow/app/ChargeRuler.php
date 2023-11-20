<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChargeRuler extends Model
{
    public const TYPE_BOLETO = 'boleto';
    public const TYPE_SUBSCRIPTION = 'subscription';
    public const TYPE_NOLIMIT = 'nolimit';
    public const TYPE_ACCESS = 'access';

    public $fillable = [
        'id',
        'platform_id',
        'type',
        'active',
        'position',
        'interval',
        'email_id',
    ];
}
