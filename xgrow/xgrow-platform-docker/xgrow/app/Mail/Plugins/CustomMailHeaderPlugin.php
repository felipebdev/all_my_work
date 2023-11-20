<?php

namespace App\Mail\Plugins;

use Swift_Events_SendEvent;
use Swift_Events_SendListener;

class CustomMailHeaderPlugin implements Swift_Events_SendListener
{
    /** @var array */
    private $config;

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

        if (config('mail.tag')) {

            $message->getHeaders()->addTextHeader('X-PM-Tag', config('mail.tag'));
        }
    }

    public function sendPerformed(Swift_Events_SendEvent $evt)
    {
    }
}
