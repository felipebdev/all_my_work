<?php

namespace App\Services\Campaign;

use App\Campaign;
use App\Jobs\CampaignJob;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CampaignScheduleExecutor
{
    private $campaign;

    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;
    }

    public function startPendingCampaigns(): bool
    {
        $pendingCampaigns = $this->getPendingCampaigns();
        $total = $pendingCampaigns->count();

        if ($total == 0) {
            return false;
        }

        foreach ($pendingCampaigns as $campaign) {
            CampaignJob::dispatch($campaign);
            $campaign->update(['status' => Campaign::STATUS_STARTED]);
        }

        return true;
    }

    private function getPendingCampaigns(): Collection
    {
        return $this->campaign->where('status', Campaign::STATUS_PENDING)
            ->where('type', Campaign::TYPE_SCHEDULED)
            ->where('start_at', '<=', DB::raw('NOW()'))
            ->get();
    }

}
