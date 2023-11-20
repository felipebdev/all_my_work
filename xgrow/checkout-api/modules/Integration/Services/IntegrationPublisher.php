<?php

namespace Modules\Integration\Services;

use BadMethodCallException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Modules\Integration\Contracts\IActionRepository;
use Modules\Integration\Contracts\IEventData;
use Modules\Integration\Contracts\IQueue;
use Modules\Integration\Enums\EventEnum;
use Modules\Integration\Events\PaymentData;
use Modules\Integration\Models\Action;
use Modules\Integration\Queue\QueueData;
use Modules\Integration\Queue\QueueHeader;
use Modules\Integration\Queue\QueuePayload;

class IntegrationPublisher
{

    private IActionRepository $repository;

    private IQueue $consumerQueue;

    private MobileNotificationGenerator $mobileNotificationGenerator;

    private ?string $correlationId = null;

    public function __construct(
        IActionRepository $repository,
        IQueue $consumerQueue,
        MobileNotificationGenerator $mobileNotificationGenerator
    ) {
        $this->repository = $repository;
        $this->consumerQueue = $consumerQueue;
        $this->mobileNotificationGenerator = $mobileNotificationGenerator;
    }

    /**
     * Add a Correlation ID on integration payloads
     *
     * @param  string|null  $correlationId
     * @return $this
     */
    public function withCorrelationId(?string $correlationId): self
    {
        $this->correlationId = $correlationId;
        return $this;
    }

    /**
     * @param  string  $event
     * @param  string  $platformId
     * @param  array  $plansId
     * @param  \Modules\Integration\Contracts\IEventData  $data
     * @return \Illuminate\Support\Collection
     */
    public function publishIntegrations(string $event, string $platformId, array $plansId, IEventData $data): Collection
    {
        if (!EventEnum::isValidValue($event)) {
            throw new BadMethodCallException('Event name is invalid');
        }

        $actionsSendToQueue = $this->repository->allByEventWithIntegration(
            $event,
            $plansId,
            $platformId
        );

        if ($data instanceof PaymentData) {
            $expoNotifications = $this->mobileNotificationGenerator
                ->generateExpoNotifications($event, $platformId, $plansId, $data);

            if ($expoNotifications) {
                $actionsSendToQueue = $actionsSendToQueue->concat($expoNotifications);
            }
        }

        Log::withContext(['total_actions' => $actionsSendToQueue->count()]);

        $sent = $actionsSendToQueue->map(function (Action $item) use ($data) {

            Log::info('Sending action to queue', [
                'action_id', $item->id ?? null,
                'action_app_id', $item->app_id ?? null,
                'action_description', $item->description ?? null,
                'action_event', $item->event ?? null,
                'action_action', $item->action ?? null,
            ]);

            $queueHeader = new QueueHeader($item, $this->correlationId);
            $queuePayload = new QueuePayload($data);
            $queueData = new QueueData($queueHeader, $queuePayload);

            return $this->consumerQueue->publish(config('apps.queue'), $queueData);
        });

        Log::info('Integration finished');

        return $sent;
    }
}
