<?php

namespace App\Logs;

use App\Payment;
use App\Recurrence;
use Illuminate\Support\Facades\Log;
use Psr\Log\LogLevel;

abstract class ChargeLog
{

    public static $favoriteChannel = 'stack_charges';

    protected static $context = [];

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

        $context = array_merge(self::$context, $context);

        Log::channel($channel)->log($level, $message, $context);
    }

    public static function withContext(array $context = [])
    {
        self::$context = array_merge(self::$context, $context);
    }

    public static function withoutContext()
    {
        self::$context = [];
    }

    public static function getContext(): array
    {
        return self::$context;
    }

    public static function unsetContextKey(string ...$keys)
    {
        foreach ($keys as $key) {
            unset(self::$context[$key]);
        }
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

    /**
     * @deprecated
     * @param  \App\Payment  $payment
     */
    public static function includePaymentContext(Payment $payment)
    {
        $paymentPlans = array();
        foreach ($payment->plans as $cod => $plan) {
            $paymentPlans[] = ['id' => $plan->id, 'name' => $plan->name];
        }
        $context = [
            'payment_id' => $payment->id,
            'subscriber_id' => $payment->subscriber_id,
            'status' => $payment->status,
            'payment_date' => $payment->payment_date,
            'installment_number' => $payment->installment_number,
            'order_number' => $payment->order_number,
            //'plans' => json_encode($paymentPlans),
            'installments' => $payment->installments,
            'platform' => optional($payment->subscriber->platform)->only(['id','name','url','customer_id']),
        ];

        self::$context = array_merge(self::$context, $context);
    }

    /**
     * @deprecated
     * @param  \App\Recurrence  $recurrence
     */
    public static function includeRecurrenceContext(Recurrence $recurrence)
    {
        $context = [
            'recurrence_id' => $recurrence->id,
            'subscriber_id' => $recurrence->subscriber_id,
            'last_payment' => $recurrence->last_payment,
            'type' => $recurrence->type,
            'payment_method' => $recurrence->payment_method,
            'total_charges' => $recurrence->total_charges,
            'order_number' => $recurrence->order_number,
            'platform' => $recurrence->plan->platform->only(['id','name','url','customer_id']),
            'subscriber_email' => $recurrence->subscriber->email ?? '',
            //'plan' => json_encode([ 'id' => $recurrence->plan->id, 'name' => $recurrence->plan->name])
            ];

        self::$context = array_merge(self::$context, $context);
    }

}
