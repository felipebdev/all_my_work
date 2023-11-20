<?php

namespace App\Repositories\Subscriptions;

use App\Subscription;
use App\Repositories\BaseRepository;
use App\Repositories\Contracts\SubscriptionRepositoryInterface;
use Illuminate\Support\Facades\DB;

class SubscriptionRepository extends BaseRepository implements SubscriptionRepositoryInterface 
{
    public function model() {
        return Subscription::class;
    }

    public function update(
        array $where, 
        array $data, 
        string $platformId = null
    ) {
        $query = $this->model->query();
        $this->setWhere($query, $where);
        
        return $query->platform($platformId)    
            ->update($data);
    }

    public function updateById(
        array $ids, 
        array $data, 
        string $platformId = null
    ) {
        return $this->model
            ->whereIn('id', $ids)
            ->platform($platformId)
            ->update($data);
    }

    public function allByOrderNumber(
        string $orderNumber, 
        array $columns = ['*'],
        string $platformId = null
    ) {
        return $this->model
            ->select($columns)
            ->platform($platformId)
            ->where(['order_number' => $orderNumber])
            ->get();
    }

    public function allBySubscriberAndPlans(
        string $subscriberId,
        array $plans,
        string $platformId = null,
        array $where = [],
        array $columns = ['*'],
        int $limit = 0,
        array $orderBy = []
    ) {
        $query = $this->model
            ->select($columns)
            ->where('subscriptions.subscriber_id', '=', $subscriberId)
            ->whereIn('subscriptions.plan_id', $plans);

        $this->setWhere($query, $where);
        $query->platform($platformId);
        $this->setOrderBy($query, $orderBy);
        
        if (!empty($limit)) {
            $query->limit($limit);
        }

        return $query->get();
    }
}