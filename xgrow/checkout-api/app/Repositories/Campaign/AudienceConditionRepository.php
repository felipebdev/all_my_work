<?php

namespace App\Repositories\Campaign;

use App\AudienceCondition;
use App\Payment;
use App\Repositories\Contracts\AudienceConditionInterface;
use App\Subscriber;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Class AudienceConditionRepository
 * @package App\Repositories\Campaign
 */
class AudienceConditionRepository implements AudienceConditionInterface
{
    private $generator;

    public function __construct(AudienceConditionSqlGenerator $audienceConditionSqlGenerator)
    {
        $this->generator = $audienceConditionSqlGenerator;
    }

    public function generateQueryFromArray(string $platformId, array $conditions = [])
    {
        $conditionsAsObjects = collect($conditions)->map(function ($condition) {
            return (object)$condition;
        });

        [$sql, $params] = $this->generator->getSqlWhereClauseFromConditions($conditionsAsObjects);

        $query = $this->baseQuerySubscribers($platformId);
        if ($sql) {
            $query->whereRaw($sql, $params);
        }

        return $query;
    }

    public function isSubscriberInAudience(int $subscriberId, int $audienceId): bool
    {
        $conditions = AudienceCondition::where('audience_id', $audienceId)->orderBy('position', 'ASC')->get();

        [$sql, $params] = $this->generator->getSqlWhereClauseFromConditions($conditions);

        $query = $this->baseQuerySubscriberExists($subscriberId);
        if ($sql) {
            $query->whereRaw($sql, $params);
        }

        $result = $query->exists();

        return $result;
    }

    protected function baseQuerySubscribers(string $platformId)
    {
        $query = Payment::select(
            'subscribers.id',
            'subscribers.name',
            'subscribers.last_acess',
            'subscribers.created_at',
            'subscribers.status AS subscriber_status',
            'subscribers.email',
            'subscribers.cel_phone',
            'subscriptions.status',
            'plans.name AS plan_name',
            'payment_plan.plan_id AS plan_id',
            'payments.status AS payment_status',
            'payments.type AS payment_type',
            'payments.type_payment AS payment_method',
            'payments.id AS payment_id'
        )
            ->selectRaw('MAX(payment_plan.created_at) AS last_payment')
            ->leftJoin('subscribers', 'subscribers.id', '=', 'payments.subscriber_id')
            ->leftJoin('payment_plan', 'payment_plan.payment_id', '=', 'payments.id')
            ->leftJoin('subscriptions', 'subscriptions.subscriber_id', '=', 'subscribers.id')
            ->leftJoin('plans', 'plans.id', '=', 'payment_plan.plan_id')
            ->where('subscribers.platform_id', $platformId)
            ->groupBy(['subscribers.id', 'plan_id'])
            ->orderBy('subscribers.id');

        return $query;
    }

    protected function baseQuerySubscriberExists(string $subscriberId)
    {
        $query = Subscriber::select('subscribers.id')
            ->leftJoin('plans', 'subscribers.plan_id', '=', 'plans.id')
            ->where('subscribers.id', $subscriberId);
        return $query;
    }

    public function generateQueryByAudienceId(string $platformId, int $audienceId): Builder
    {
        $conditions = AudienceCondition::where('audience_id', $audienceId)->orderBy('position', 'ASC')->get();

        [$sql, $params] = $this->generator->getSqlWhereClauseFromConditions($conditions);

        $query = $this->baseQuerySubscribers($platformId);
        if ($sql) {
            $query->whereRaw($sql, $params);
        }

        return $query;
    }

    public function subscribersFromAudienceIds(string $platformId, iterable $audienceIds): Collection
    {
        $subscribers = new Collection();
        foreach ($audienceIds as $audienceId) {
            $query = $this->generateQueryByAudienceId($platformId, $audienceId);
            $newSubscribers = $query->get();

            $newSubscribers->transform(function ($subscriber) use ($audienceId) {
                $subscriber['audience_id'] = $audienceId;
                return $subscriber;
            });

            $subscribers = $subscribers->merge($newSubscribers);
        }
        return $subscribers;
    }

}
