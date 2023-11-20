<?php

namespace Modules\Integration\Queue\Jobs;

use App\Logs\XgrowLog;
use BadMethodCallException;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Integration\Contracts\IActionRepository;
use Modules\Integration\Contracts\IEventData;
use Modules\Integration\Contracts\IQueue;
use Modules\Integration\Enums\EventEnum;
use Modules\Integration\Queue\QueueData;
use Modules\Integration\Queue\QueueHeader;
use Modules\Integration\Queue\QueuePayload;

class HandleIntegration implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    /**
     * @var string
     */    
    public $connection = 'redis';

    /**
     * @var string
     */
    public $queue = 'xgrow-jobs:integrations:';

    /**
     * @var string
     * @see Modules\Integration\Enums\EventEnum
     */
    private $event;

    /**
     * @var string
     */
    private $platformId;

    /**
     * @var array
     */
    private $plansId;

    /**
     * @var IEventData
     */
    private $data;

    /**
     * @var IActionRepository
     */
    private $repository;

    /**
     * @var IQueue
     */
    private $consumerQueue;

    /**
     * @param string $event Modules\Integration\Enums\EventEnum
     * @param string $platformId 
     * @param array $plansId
     * @param IEventData $data
     */
    public function __construct(
        string $event,
        string $platformId,
        array $plansId,
        IEventData $data
    ) {
        $this->repository = app()->make(IActionRepository::class);
        $this->consumerQueue = app()->make(IQueue::class);
        $this->event = $event;
        $this->platformId = $platformId;
        $this->plansId = $plansId;
        $this->data = $data;
    }
    
    public function handle()
    {
        try {
            if (!EventEnum::isValidValue($this->event)) {
                throw new BadMethodCallException('Event name is invalid');
            }

            $actionsSendToQueue = $this->repository->allByEventWithIntegration(
                $this->event,
                $this->plansId,
                $this->platformId            
            );
            
            $actionsSendToQueue->each(function ($item) {
                $queueHeader = new QueueHeader($item);
                $queuePayload = new QueuePayload($this->data);
                $queueData = new QueueData($queueHeader, $queuePayload);
                $this->consumerQueue->publish(
                    config('apps.queue'),
                    $queueData
                );
            });
        } catch (Exception $e) {
            XgrowLog::xError(
                'HandleIntegrationJob > ',
                $e,
                [],
                'integration'
            );
        }
    }

    public function shouldQueue() {
        return (
            env('INTEGRATION_QUEUE_CONNECTION') !== null 
            && env('INTEGRATION_QUEUE_CONNECTION') !== ''
        );
    }
}
