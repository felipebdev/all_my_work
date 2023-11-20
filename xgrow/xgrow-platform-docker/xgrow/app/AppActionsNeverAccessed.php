<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Modules\Integration\Models\Action;
use App\Utils\TriggerIntegrationJob;

/**
 *
 */
class AppActionsNeverAccessed extends Model
{
    use TriggerIntegrationJob;
    /**
     * @var string
     */
    protected $table = 'app_actions_never_accessed';

    /**
     * @var string[]
     */
    protected $fillable = [
        'id',
        'app_actions_id',
        'subscriber_id',
        'last_event'
    ];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function action()
    {
        return $this->belongsTo(Action::class, 'app_actions_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subscriber()
    {
        return $this->belongsTo(Subscriber::class, 'subscriber_id', 'id');
    }

    /**
     * @param array $actions
     */
    public function storeActions(array $actions)
    {
        foreach ($actions as $action) {
            foreach ($action['subscribers'] as $subscriber) {

                $lastAction = $this->where('app_actions_id', $action['action_id'])
                    ->where('subscriber_id', $subscriber['subscriber_id'])
                    ->whereRaw("DATEDIFF(now(), last_event) <= {$action['days_never_accessed_action']}")
                    ->first();

                if (!$lastAction) {

                    $this->where('app_actions_id', $action['action_id'])
                        ->where('subscriber_id', $subscriber['subscriber_id'])
                        ->where('last_event', '<', $action['next_event'])
                        ->delete();

                    $subscriberObject = Subscriber::find($subscriber['subscriber_id']);

                    $this->triggerNeverAccessEvent($subscriberObject);

                    $this->create([
                        'app_actions_id' => $action['action_id'],
                        'subscriber_id' => $subscriber['subscriber_id'],
                        'last_event' => $action['next_event'],
                    ]);
                }
            }
        }
    }
}
