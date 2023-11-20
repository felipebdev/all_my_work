<?php

namespace App\Jobs\Tmb;

use App\Http\Controllers\Api\Webhooks\TmbService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TmbJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $request;

    public function __construct(array $request)
    {
        $this->request = $request;
    }

    public function handle(TmbService $tmbService)
    {
        $tmbService->process($this->request);
    }
}
