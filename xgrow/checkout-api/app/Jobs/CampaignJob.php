<?php

namespace App\Jobs;

use App\Campaign;
use App\Services\Campaign\CampaignService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CampaignJob implements ShouldQueue
{
    private $campaign;

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(Campaign $campaing)
    {
        $this->connection = 'redis'; // Queueable
        $this->queue = 'xgrow-jobs:campaign:scheduled'; // Queueable

        $this->campaign = $campaing;
    }

    public function handle(CampaignService $campaignService)
    {
        $campaignService->decideAndSendCampaignMessages($this->campaign);

        $this->campaign->update(['status' => Campaign::STATUS_CONCLUDED]);
    }

}
