<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @mixin \App\Services\Whatsapp\WhatsappPublisher
 */
class Whatsapp extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \App\Services\Whatsapp\WhatsappPublisher::class;
    }
}
