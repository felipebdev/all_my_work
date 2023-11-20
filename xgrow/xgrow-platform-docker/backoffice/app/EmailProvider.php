<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailProvider extends Model
{

    use HasFactory;

    public const DRIVERS = [
        'log',
        'smtp',
        'mailgun',
        'mandrill',
        'ses',
        'postmark',
    ];

    protected $fillable = [
        'id',
        'name',
        'description',
        'from_name',
        'from_address',
        'service_tags',
        'driver',
        'settings',
    ];
}
