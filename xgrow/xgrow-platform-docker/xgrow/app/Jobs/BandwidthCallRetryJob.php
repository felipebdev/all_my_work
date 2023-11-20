<?php

namespace App\Jobs;

use App\Services\Bandwidth\BandwidthVoiceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BandwidthCallRetryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    private $groupUuid;

    public function __construct(string $groupUuid)
    {
        $this->groupUuid = $groupUuid;
    }

    public function handle(BandwidthVoiceService $bandwidthVoiceService)
    {
        $bandwidthVoiceService->retryCallingByGroupUuid($this->groupUuid);
    }
}
