<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentLog extends Model
{
    //use HasFactory;

    const AGENT_SUBSCRIBER = 'S'; // request from Subscriber
    const AGENT_CLIENT = 'C'; // request from Client/platform owner
}
