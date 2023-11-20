<?php

namespace App\Mail\Plugins;

use Swift_Events_SendEvent;
use Swift_Events_SendListener;

class CustomMailHeaderPlugin implements Swift_Events_SendListener
{
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function beforeSendPerformed(Swift_Events_SendEvent $evt)
    {
        $message = $evt->getMessage();
        $headers = $this->config['headers'] ?? [];
        foreach ($headers as $header => $value) {
            $message->getHeaders()->addTextHeader($header, $value);
        }

        $tag = config('mail.tag');
        if ($tag) {
            $message->getHeaders()->addTextHeader('X-PM-Tag', $tag);
        }

    }

    public function sendPerformed(Swift_Events_SendEvent $evt)
    {
    }
}
