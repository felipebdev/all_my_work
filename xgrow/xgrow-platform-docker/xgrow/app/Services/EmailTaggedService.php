<?php


namespace App\Services;


use App\Logs\XgrowLog;
use Illuminate\Contracts\Mail\Mailable as MailableContract;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class EmailTaggedService
{
    /**
     * @param  string  $platformId
     * @param  string  $serviceTag
     * @param  \Illuminate\Contracts\Mail\Mailable  $mail
     * @param  array  $recipients Additional recipients if set on Mailable (optional), required if not set on Mailable
     */
    public static function mail(string $platformId, string $serviceTag, MailableContract $mail, array $recipients = [])
    {
        $config = self::rememberProviderConfigByServiceTag($serviceTag);
        if ($config) {
            Config::set('mail', $config); // replaces default provider config
        }

        Mail::to($recipients)->send($mail);

        XgrowLog::mail()->debug('>', [
            'platform_id' => $platformId,
            'subject' => $mail->subject,
            'recipients' => $recipients
        ]);
    }

    public static function rememberProviderConfigByServiceTag(string $serviceTag)
    {
        return Cache::driver('redis')->remember("FEATURE:MAIL_SERVICE_TAG:{$serviceTag}", 30,
            function () use ($serviceTag) {
                $entry = DB::table('email_providers')
                    ->whereJsonContains('service_tags', $serviceTag)
                    ->first();

                if (!$entry) {
                    return null;
                }

                $data = json_decode($entry->settings, $associative = true);

                $config = array_merge([
                    'driver' => $entry->driver,
                    'from' => [
                        'address' => $entry->from_address,
                        'name' => $entry->from_name
                    ],
                ], $data);

                return $config;
            }
        );
    }

}
