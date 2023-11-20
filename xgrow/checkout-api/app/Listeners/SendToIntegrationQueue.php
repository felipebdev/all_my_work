<?php

namespace App\Listeners;

use stdClass;
use App\Events\BaseEvent;
use Illuminate\Support\Str;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\Contracts\IntegrationServiceInterface;
use App\Repositories\Contracts\IntegrationRepositoryInterface;

/**
 * @deprecated v0.23
 */
class SendToIntegrationQueue implements ShouldQueue
{
    public $connection = 'redis';
    public $queue = 'xgrow-jobs:integrations:';
    private $integrationRepository;
    private $integrationService;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(
        IntegrationRepositoryInterface $integrationRepository,
        IntegrationServiceInterface $integrationService
    ) {
        $this->integrationRepository = $integrationRepository;
        $this->integrationService = $integrationService;
    }

    /**
     * Handle the event.
     *
     * @param  BaseEvent  $event
     * @return void
     */
    public function handle(BaseEvent $event)
    {
        $integrations = $this->integrationRepository->findActiveByPlatformAndTrigger(
            $event->platform->id,
            $event->trigger
        );

        $trigger = Str::snake($event->trigger);
        $data = new stdClass();
        $data->platform_id = $event->platform->id;
        $data->trigger = $event->trigger;
        $data->metadata = $event->metadata;
        $data->actions = [];

        foreach($integrations as $integration) {
            $sourceTokenIsJson = isJSON($integration->source_token) ?? false;
            $sourceToken = ($sourceTokenIsJson) ? json_decode($integration->source_token)->api_key ?? '': $integration->source_token;
            $info = new stdClass();
            $info->id_integration = $integration->id_webhook;
            $info->integration_type = $integration->id_integration;
            $info->url_webhook = $integration->url_webhook;
            $info->source_token = $sourceToken;
            $info->extra = ($sourceTokenIsJson) ? $integration->source_token : '';
            
            if ($sourceTokenIsJson) {
                $token = json_decode($integration->source_token);
                if (is_object($token->$trigger) && count(get_object_vars($token->$trigger)) > 0) {
                    $actions = (object) $token->$trigger;
                    foreach ($actions as $key => $action) {
                        $info->action = $key;
                        $data->actions[] = clone $info;
                    }
                }
            }
            else {
                $info->action = $trigger;
                array_push($data->actions, $info);
            }
        }

        if (!empty($data->actions)) {
            $this->integrationService->sendToBullMQ($event->queue, $data);
        }
    }

    public function shouldQueue() {
        return (env('INTEGRATION_QUEUE_CONNECTION') !== null && env('INTEGRATION_QUEUE_CONNECTION') !== '');
    }
}
