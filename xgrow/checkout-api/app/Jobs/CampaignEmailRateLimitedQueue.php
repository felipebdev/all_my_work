<?php

namespace App\Jobs;

use App\Services\CampaignEmail\SendEmailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Redis\LimiterTimeoutException;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class CampaignEmailRateLimitedQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $platformId;
    private $subject;
    private $text;
    private $recipient;
    private $replyto;

    public function __construct($platformId, string $subject, string $text, string $recipient, ?string $replyTo)
    {
        $this->connection = 'redis'; // Queueable
        $this->queue = 'xgrow-jobs:campaign:email'; // Queueable

        $this->platformId = $platformId;
        $this->subject = $subject;
        $this->text = $text;
        $this->recipient = $recipient;
        $this->replyto = $replyTo;
    }

    /**
     * Determine the time at which the job should timeout.
     *
     * @return \DateTime
     */
    public function retryUntil()
    {
        return now()->addSeconds(60 * 5); // 5 minutes to execute
    }

    public function handle()
    {
        $limitPerMinute = 5;

        Redis::throttle('xgrow-jobs:throttle:email')
            ->allow($limitPerMinute)
            ->every(60)
            ->then(function () {
                $replyTo = $this->replyto ?? $this->recipient;

                $sendEmailService = new SendEmailService($this->platformId);
                $sendEmailService->sendSingleEmail($this->subject, $this->text, $this->recipient, $replyTo);
            }, function ($error) {
                if ($error instanceof LimiterTimeoutException) {
                    // Throttling, just wait
                } else {
                    Log::error('xgrow-jobs:email error', ['error' => $error]);
                }

                return $this->release(10);
            });
    }
}
