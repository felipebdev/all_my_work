<?php

namespace App\Logs;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Psr\Log\LogLevel;

abstract class ChargeLog
{

    protected static $favoriteChannel = 'charges';

    public static function emergency($message, array $context = [], string $channel = null)
    {
        self::log(LogLevel::EMERGENCY, $message, $context, $channel);
    }

    public static function alert($message, array $context = [], string $channel = null)
    {
        self::log(LogLevel::ALERT, $message, $context, $channel);
    }

    public static function critical($message, array $context = [], string $channel = null)
    {
        self::log(LogLevel::CRITICAL, $message, $context, $channel);
    }

    public static function error($message, array $context = [], string $channel = null)
    {
        self::log(LogLevel::ERROR, $message, $context, $channel);
    }

    public static function warning($message, array $context = [], string $channel = null)
    {
        self::log(LogLevel::WARNING, $message, $context, $channel);
    }

    public static function notice($message, array $context = [], string $channel = null)
    {
        self::log(LogLevel::NOTICE, $message, $context, $channel);
    }

    public static function info($message, array $context = [], string $channel = null)
    {
        self::log(LogLevel::INFO, $message, $context, $channel);
    }

    public static function debug($message, array $context = [], string $channel = null)
    {
        self::log(LogLevel::DEBUG, $message, $context, $channel);
    }

    public static function log($level, $message, array $context = [], string $channel = null)
    {
        $channel = self::getChannel($channel);

        $commandCorrelationId = Config::get('command_correlation_id');
        if ($commandCorrelationId) {
            $context = array_merge(['command_correlation_id' => $commandCorrelationId], $context);
        }

        Log::channel($channel)->log($level, $message, $context);
    }

    protected static function getChannel(string $channel = null): string
    {
        $channel = $channel ?? self::$favoriteChannel; // use defined $channel, favorite channel otherwise

        $channels = array_keys(config('logging.channels'));
        if (empty($channel) || !in_array($channel, $channels)) {
            // if channel is invalid, use global default
            $channel = env('LOG_CHANNEL', 'stack');
        }

        return $channel;
    }

}
