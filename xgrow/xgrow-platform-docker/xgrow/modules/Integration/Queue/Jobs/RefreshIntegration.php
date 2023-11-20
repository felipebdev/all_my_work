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
use Modules\Integration\Events\RefreshData;
use Modules\Integration\Queue\QueueData;
use Modules\Integration\Queue\QueueHeader;
use Modules\Integration\Queue\QueuePayload;

/**
 * Reprocess integration 
 * @example webhook history tab
 */
class RefreshIntegration implements ShouldQueue
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
     * @var int
     */
    private $actionId;

    /**
     * @var IEventData
     */
    private $data;

    /**
     * @var string|null
     */
    private $platformId;

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
        string $actionId,
        object $data,
        ?string $platformId = null
    ) {
        $this->repository = app()->make(IActionRepository::class);
        $this->consumerQueue = app()->make(IQueue::class);
        $this->actionId = $actionId;
        $this->platformId = $platformId;
        $this->data = new RefreshData($data);
    }
    
    public function handle()
    {
        try {
            $action = $this->repository->findByIdWithIntegration(
                $this->actionId, 
                $this->platformId
            );
            
            $queueHeader = new QueueHeader($action);
            $queuePayload = new QueuePayload($this->data);
            $queueData = new QueueData($queueHeader, $queuePayload);
            $this->consumerQueue->publish(
                config('apps.queue'),
                $queueData
            );
        } catch (Exception $e) {
            XgrowLog::xError(
                'RefreshIntegrationJob > ',
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
