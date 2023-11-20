<?php

namespace App\Services\Bandwidth;

use App\Services\Contracts\RejectableInterface;
use BandwidthLib\BandwidthClient;
use BandwidthLib\Configuration;

use function collect;

abstract class BandwidtBaseService implements RejectableInterface
{

    protected $rejected = [];

    protected $smsUsername;
    protected $smsPassword;
    protected $smsApplicationId;
    protected $smsFromNumber;
    protected $smsAccountId;

    protected $voiceUsername;
    protected $voicePassword;
    protected $voiceApplicationId;
    protected $voiceFromNumber;
    protected $voiceAccountId;

    public function __construct()
    {
        $this->smsUsername = config('ads.bandwidth.sms.username');
        $this->smsPassword = config('ads.bandwidth.sms.password');
        $this->smsApplicationId = config('ads.bandwidth.sms.application_id');
        $this->smsFromNumber = config('ads.bandwidth.sms.from_number');
        $this->smsAccountId = config('ads.bandwidth.sms.account_id');

        $this->voiceUsername = config('ads.bandwidth.voice.username');
        $this->voicePassword = config('ads.bandwidth.voice.password');
        $this->voiceApplicationId = config('ads.bandwidth.voice.application_id');
        $this->voiceFromNumber = config('ads.bandwidth.voice.from_number');
        $this->voiceAccountId = config('ads.bandwidth.voice.account_id');
    }

    public function getRejected(): array
    {
        return $this->rejected;
    }

    protected function getBandwidthClient(): BandwidthClient
    {
        $config = new Configuration([
            'messagingBasicAuthUserName' => $this->smsUsername,
            'messagingBasicAuthPassword' => $this->smsPassword,
            'voiceBasicAuthUserName' => $this->voiceUsername,
            'voiceBasicAuthPassword' => $this->voicePassword,
        ]);
        $client = new BandwidthClient($config);

        return $client;
    }

    protected function filterAndPrepareNumbers(array $numbers): array
    {
        return collect($numbers)->map(function ($number) {
            $stripped = preg_replace('/[^0-9]/', '', $number);

            if (strlen($stripped) >= 7 && strlen($stripped) <= 15) {
                return "+{$stripped}";
            }

            $this->rejected[] = $number;
            return null;
        })->filter()->toArray();
    }


    /**
     * Truncated exponential backoff with jitter function returning wait time in ms
     * Uses the formula base_time^n and add a random jitter
     *
     * @param  int  $count
     * @return int
     */
    protected function calculateExponentialBackoffTime(int $count = 0): int
    {
        $cap = 120000; // ceil wait time to 120s
        $baseTime = 4; // base 4ms
        $exponential = pow($baseTime, $count); //4^n: 1ms, 4ms, 16ms, 64ms, 256ms, 1.024s, 4.096s, 16.384s, 65.536s, //262.144s
        $capped = min($cap, $exponential); // limit exponential
        $jitter = $capped * rand(0, 50) / 100; // random jitter (0% to 50%)
        $time = $capped + $jitter;
        return $time;
    }
}
