<?php

namespace App\Services\Actions;

use App\Repositories\Leads\LeadAbandonedCartRepository;
use App\Utils\TriggerIntegrationJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

use function gethostname;

class AbandonedCartAction
{

    use TriggerIntegrationJob;

    private LeadAbandonedCartRepository $leadAbandonedCartRepository;

    public function __construct()
    {
        $this->leadAbandonedCartRepository = app()->make(LeadAbandonedCartRepository::class);
    }

    public function __invoke(): array
    {
        Log::withContext(['schedule-trace-id' => (string) Str::uuid()]);
        Log::withContext(['running_origin' => 'abandoned_cart']);
        Log::withContext(['hostname-dispatcher' => gethostname()]);

        Log::info('checkout:abandoned_cart:starting');

        $leadsProcessed = $this->processAbandonedLeads();

        Log::info('checkout:abandoned_cart:ended', [
            'leads_processed_count' => count($leadsProcessed),
        ]);

        Log::withoutContext();

        return $leadsProcessed;
    }

    private function processAbandonedLeads(): array
    {
        $leadsProcessed = [];

        $leads = $this->leadAbandonedCartRepository->listAbandonedLeads();

        foreach ($leads as $lead) {
            $this->processSingleAbandonedLead($lead);

            $leadsProcessed[] = $lead->id;
        }

        return $leadsProcessed;
    }

    private function processSingleAbandonedLead($lead): void
    {
        Log::info('checkout:abandoned_cart:processing', [
            'lead_id' => $lead->id,
            'lead_email' => $lead->email,
        ]);

        $this->triggerCartAbandonedEvent($lead);

        $this->leadAbandonedCartRepository->markLeadAsAbandoned($lead->id);
    }

}
