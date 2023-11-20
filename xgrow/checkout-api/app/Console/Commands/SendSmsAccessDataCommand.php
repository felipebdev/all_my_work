<?php

namespace App\Console\Commands;

use App\Jobs\SendSmsAccessDataJob;
use App\Subscriber;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendSmsAccessDataCommand extends Command
{
    protected $signature = 'xgrow:send-sms-access {platform_id} '.
    '{--bitly-token= : Bit.ly token (required)} '.
    '{--text= : Additional text (optional)} '.
    '{--subscriber_id= : Subscriber Id (optional)} '.
    '{--dry-run : Run in test mode (no real sending, optional)} ';

    protected $description = 'Send access data using SMS';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $time = Carbon::now()->toISOString();

        $platformId = $this->argument('platform_id');
        $subscriberId = $this->option('subscriber_id');

        if (!$this->option('bitly-token')) {
            $this->error('Option --bitly-token required');
            return Command::FAILURE;
        }

        $bitlyToken = $this->option('bitly-token');

        $text = $this->option('text') ?? '';

        $dryRun = $this->option('dry-run');

        $subscribers = $this->getSubscribers($platformId, $subscriberId);

        $this->info("All logs will be sent to sms-access-log-{$time}.csv");

        foreach ($subscribers as $subscriber) {
            SendSmsAccessDataJob::dispatch($time, $platformId, $subscriber, $bitlyToken, $dryRun, $text);
        }

        return Command::SUCCESS;
    }

    private function getSubscribers(string $platformId, string $subscriberId = null)
    {
        return Subscriber::query()
            ->whereNull('login')
            ->where('platform_id', $platformId)
            ->where('status', Subscriber::STATUS_ACTIVE)
            ->where(function($query) use ($subscriberId) {
                if( !empty($subscriberId) ) {
                    $query->where('id', $subscriberId);
                }
            })
            ->lazy();
    }


}
