<?php

namespace App\Services\BulkGate;

use BulkGate\Sdk\Message\Send;
use BulkGate\Sdk\Message\Sms;
use BulkGate\Sdk\Sender;

class BukGateSender
{
    private Sender $sender;

    public function __construct(Sender $sender)
    {
        $this->sender = $sender;
    }

    public function sendMessage(string $phoneNumber, string $text): Send
    {
        return $this->sender->send(new Sms($phoneNumber, $text));
    }
}
