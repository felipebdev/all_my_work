<?php

namespace App\Services\Campaign;

use App\Campaign;
use App\Jobs\CampaignJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CampaignScheduleExecutor
{
    private $campaign;

    public function __construct()
    {
        $this->campaign = Campaign::where('status', Campaign::STATUS_PENDING)
            ->where('type', Campaign::TYPE_SCHEDULED)
            ->where('start_at', '<=', Carbon::now())
            ->get();
    }

    public function startPendingCampaigns(): bool
    {
        try {
            $total = $this->campaign->count();
            if ($total == 0) return false;

            foreach ($this->campaign as $campaign) {
                CampaignJob::dispatch($campaign);
                $campaign->update(['status' => Campaign::STATUS_STARTED]);
            }
            return true;
        }catch (\Exception $e){
            Log::error('Erro no CampaignSchedulerExceutor > startPendingCampaigns(): ' . ' | System error: ' . $e->getMessage());
        }
    }
}
