<?php

namespace App\Repositories\Producers;

use App\PlatformUser;
use App\Producer;
use App\Repositories\BaseRepository;
use App\Repositories\Contracts\ProducerRepositoryInterface;
use App\Services\Objects\ProducerReportFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ProducerRepository extends BaseRepository implements ProducerRepositoryInterface
{

    public function model()
    {
        return Producer::class;
    }

    public function reportProducers(string $platformId, ProducerReportFilter $filters): Builder
    {
        $query = $this->model
            ->selectRaw('producers.id AS producers_id')
            ->selectRaw('platforms_users.id AS platforms_users_id')
            ->selectRaw('platforms_users.name AS platforms_users_name')
            ->selectRaw('platforms_users.email AS platforms_users_email')
            ->selectRaw('products.name AS products_name')
            ->selectRaw('producer_products.status AS producer_products_status')
            ->selectRaw('producer_products.canceled_at AS producer_products_canceled_at')
            ->selectRaw('producer_products.percent AS producer_products_percent')
            //->selectRaw('recent_access.date AS recent_access_date')
            ->where('producers.platform_id', $platformId)
            ->leftJoin('producer_products', 'producer_products.producer_id', '=', 'producers.id')
            ->leftJoin('products', 'producer_products.product_id', '=', 'products.id')
            ->leftJoin('platforms_users', 'platforms_users.id', '=', 'producers.platform_user_id')
            //->leftJoin(DB::raw('(SELECT
            //        user_id,
            //        max(access_logs.created_at) as date
	        //    FROM access_logs
            //    WHERE user_type = "platforms_users"
            //    GROUP BY user_id
            //) as recent_access'), function($join){
            //    $join->on('recent_access.user_id', '=', 'platforms_users.id');
            //})
            ->when($filters->productsId, function ($query, $productsId) {
                $query->whereIn('products.id', $productsId);
            })
            ->when($filters->producerProductStatusId, function ($query, $producerProductStatusId) {
                $query->whereIn('producer_products.status', $producerProductStatusId);
            })
            ->when($filters->searchTerm, function ($query, $searchTerm) {
                $query->where(function ($query) use ($searchTerm) {
                    $query->orWhere('platforms_users.email', 'like', '%'.$searchTerm.'%')
                        ->orWhere('platforms_users.name', 'like', '%'.$searchTerm.'%')
                        ->orWhere('products.name', 'like', '%'.$searchTerm.'%');
                });
            });

        return $query;
    }

    public function getPlatformUserByProducerId(int $producerId): PlatformUser
    {
        return $this->findById($producerId)->platformUser;
    }

}
