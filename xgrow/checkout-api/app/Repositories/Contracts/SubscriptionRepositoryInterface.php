<?php

namespace App\Repositories\Contracts;
interface SubscriptionRepositoryInterface extends BaseRepositoryInterface
{
    public function update(
        array $where, 
        array $data, 
        string $platformId = null
    );

    public function updateById(
        array $ids, 
        array $data,
        string $platformId = null
    );

    public function allByOrderNumber(
        string $orderNumber, 
        array $columns = ['*'],
        string $platformId = null
    );

    public function allBySubscriberAndPlans(
        string $subscriberId,
        array $plans,
        string $platformId = null,
        array $where = [],
        array $columns = ['*'],
        int $limit = 0,
        array $orderBy = []
    );
}
