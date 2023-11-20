<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Mail\PendingMail to($users)
 * @method static \Illuminate\Mail\PendingMail bcc($users)
 * @method static void raw(string $text, $callback)
 * @method static void send(\Illuminate\Contracts\Mail\Mailable|string|array $view, array $data = [], \Closure|string $callback = null)
 * @method static array failures()
 * @method static mixed queue(\Illuminate\Contracts\Mail\Mailable|string|array $view, string $queue = null)
 * @method static mixed later(\DateTimeInterface|\DateInterval|int $delay, \Illuminate\Contracts\Mail\Mailable|string|array $view, string $queue = null)
 * @method static void assertSent(string $mailable, callable|int $callback = null)
 * @method static void assertNotSent(string $mailable, callable|int $callback = null)
 * @method static void assertNothingSent()
 * @method static void assertQueued(string $mailable, callable|int $callback = null)
 * @method static void assertNotQueued(string $mailable, callable $callback = null)
 * @method static void assertNothingQueued()
 * @method static \Illuminate\Support\Collection sent(string $mailable, \Closure|string $callback = null)
 * @method static bool hasSent(string $mailable)
 * @method static \Illuminate\Support\Collection queued(string $mailable, \Closure|string $callback = null)
 * @method static bool hasQueued(string $mailable)
 *
 * @see \Illuminate\Mail\Mailer
 * @see \Illuminate\Support\Testing\Fakes\MailFake
 */
class XgrowMail extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'xgrow-mail';
    }
}
