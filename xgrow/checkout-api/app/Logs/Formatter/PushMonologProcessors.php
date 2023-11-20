<?php

namespace App\Logs\Formatter;

use Monolog\Logger;
use Monolog\Processor\GitProcessor;
use Monolog\Processor\HostnameProcessor;
use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\UidProcessor;
use Monolog\Processor\WebProcessor;

class PushMonologProcessors
{
    public function __invoke($logger)
    {
        foreach ($logger->getHandlers() as $handler) {
            //$handler->pushProcessor(new HostnameProcessor());
            //$handler->pushProcessor(new GitProcessor());
            $handler->pushProcessor(new IntrospectionProcessor(Logger::DEBUG, ['Illuminate\\']));
            //$handler->pushProcessor(new WebProcessor());
        }
    }
}
