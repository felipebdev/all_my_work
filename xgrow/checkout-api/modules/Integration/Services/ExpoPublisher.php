<?php

namespace Modules\Integration\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Integration\Enums\ActionEnum;
use Modules\Integration\Enums\TypeEnum;
use Modules\Integration\Services\Objects\ExpoMessage;
use Modules\Messaging\Contracts\ProducerQueueInterface;
use stdClass;

use function config;

class ExpoPublisher
{

    private ProducerQueueInterface $producer;

    private ?string $correlationId;

    public function __construct(ProducerQueueInterface $producer)
    {
        $this->producer = $producer;
    }

    public function withCorrelationId(string $correlationId): self
    {
        $this->correlationId = $correlationId;
        return $this;
    }

    public function pushNotification(string $platformId, ExpoMessage $message, $expoTokens): string
    {
        $json = json_encode([
            'header' => [
                'date' => Carbon::now()->toIso8601ZuluString(),
                'correlation_id' => $this->correlationId ?? (string) Str::uuid(),
                'app' => [
                    'platform_id' => $platformId,
                    'event' => 'anyEvent', // placeholder
                    'action' => ActionEnum::TRIGGER_EXPO,
                    'integration' => [
                        'type' => TypeEnum::EXPO,
                        'metadata' => [
                            'expoTokens' => array_values($expoTokens->toArray()), // send same message to all users
                            'messageTitle' => $message->title,
                            'messageBody' => $message->body,
                            'messageData' => new stdClass(),
                        ]
                    ]
                ]
            ],
            'payload' => [
                'data' => []
            ]
        ]);

        $this->producer->queue(config('apps.queue'), $json);

        Log::debug('expo:generic:published', ['json' => $json]);

        return $json;
    }
}
