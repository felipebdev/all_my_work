<?php

namespace Modules\Integration\Queue\Jobs;

use App\Helpers\LogContext;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\Integration\Contracts\IEventData;
use Modules\Integration\Services\IntegrationPublisher;

class HandleIntegration implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    //public $connection = 'redis';

    public $queue = 'xgrow-jobs:integrations-checkout';

    /**
     * @see \Modules\Integration\Enums\EventEnum
     */
    public string $event;

    public string $platformId;

    public array $plansId;

    public IEventData $data;

    public $context = [];

    /**
     * @param  string  $event  Modules\Integration\Enums\EventEnum
     * @param  string  $platformId
     * @param  array  $plansId
     * @param  IEventData  $data
     */
    public function __construct(
        string $event,
        string $platformId,
        array $plansId,
        IEventData $data
    ) {
        $this->context = LogContext::get();

        $this->event = $event;
        $this->platformId = $platformId;
        $this->plansId = $plansId;
        $this->data = $data;
    }

    public function handle(IntegrationPublisher $integrationPublisher)
    {
        LogContext::add($this->context);

        Log::withContext(['event' => $this->event ?? null]);
        Log::withContext(['platform_id' => $this->platformId ?? null]);
        //Log::withContext(['plans_id' => json_encode($this->plansId ?? null)]);
        //Log::withContext(['data' => $this->data->getAttributes() ?? null]); // Has PII data

        Log::debug('Handling integration');

        try {
            $integrationPublisher->withCorrelationId($this->context['correlation_id'] ?? null);

            return $integrationPublisher->publishIntegrations(
                $this->event,
                $this->platformId,
                $this->plansId,
                $this->data
            );
        } catch (Exception $e) {
            Log::error('HandleIntegrationJob exception', [
                'exception_file' => $e->getFile(),
                'exception_line' => $e->getLine(),
                'exception_message' => $e->getMessage(),
                'success' => false,
            ]);
        }
    }

    public function shouldQueue()
    {
        return env('INTEGRATION_QUEUE_CONNECTION') !== null
            && env('INTEGRATION_QUEUE_CONNECTION') !== '';
    }
}
