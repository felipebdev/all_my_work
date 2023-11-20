<?php

namespace App\Repositories\Affiliations;

use App\AffiliationSettings;
use App\Plan;
use App\Platform;
use App\Product;
use App\ProductLink;
use App\Producer;
use App\ProducerProduct;
use App\Repositories\Affiliations\Objects\AffiliationFilter;
use App\Repositories\CoProducersAffiliations\AbstractProducersRepository;
use App\Services\Checkout\CheckoutBaseService;
use App\Services\Checkout\WithdrawService;
use App\Services\Objects\PeriodFilter;
use App\Services\UrlShortenerService;
use Carbon\Carbon;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class AffiliationsRepository extends AbstractProducersRepository
{
    private $urlShortenerService;

    public function __construct(UrlShortenerService $urlShortenerService)
    {
        $this->urlShortenerService = $urlShortenerService;
    }
    /**
     * @return string
     */
    public function getType(): string
    {
        return 'A';
    }

    /**
     * @return string
     */
    public function getActingAs(): string
    {
        return 'affiliate';
    }

    /**
     * @param  array  $request
     * @return mixed|null
     * @throws GuzzleException
     */
    public function report(array $request)
    {
        return $this->callFinancialApi('financial/affiliations', $request);
    }

    public function affiliateRanking(array $request)
    {
        return $this->callFinancialApi('financial/affiliate-ranking', $request);
    }

    public function getPlatformsAffiliations(string $term = null)
    {
        $query = Platform::select(
            'platforms.id as platform_id',
            'platforms.name as platform_name',
            'platforms.url as platform_url',
            'platforms.cover as platform_cover',
            'files.filename',
            'producers.id as producer_id',
            'producer_products.status as status',
            'producer_products.id as producer_products_id',
            'producer_products.contract_limit',
            'producer_products.percent',
            'products.name as product_name',
        )
            ->join('producers', 'platforms.id', '=', 'producers.platform_id')
            ->join('producer_products', 'producers.id', '=', 'producer_products.producer_id')
            ->join('products', 'products.id', '=', 'producer_products.product_id')
            ->where('producers.type', 'A')
            ->where('producers.platform_user_id', Auth::user()->id);

        if ($term) {

            $query->where(function (Builder $q) use ($term) {
                return $q
                    ->where('products.name', 'like', "%$term%")
                    ->orWhere('platforms.name', 'like', "%$term%");
            });
        }

        return $query;
    }

    public function listProductsAffiliates(array $request): array
    {
        $user_id = isset($request['user_id']) ? $request['user_id'] : Auth::user()->id;
        $platform_id = request()->route()->parameters()['platformId']; //isset($request['platform_id']) ? $request['platform_id'] : Auth::user()->platform_id;
        $product_name = isset($request['product_name']) ? $request['product_name'] : '';
        $affiliation_status = isset($request['affiliation_status']) ? $request['affiliation_status'] : '';
        $offset = isset($request['offset']) ? $request['offset'] : 10;

        $data = DB::table('products')->select(
            'products.id as product_id',
            'producers.id as producers_id',
            'producer_products.id  as producer_produtcs_id',
            'products.name',
            'files.filename',
            'products.platform_id',
            'producer_products.created_at as affiliate_creation',
            'producer_products.status'
        )
            ->join('producer_products', 'producer_products.product_id', '=', 'products.id')
            ->join('producers', 'producers.id', '=', 'producer_products.producer_id')
            ->leftJoin('files', 'files.id', '=', 'products.image_id')
            ->when($product_name <> '', function ($query) use ($product_name) {
                return $query->where('products.name', 'like', '%'.$product_name.'%');
            })
            ->when($affiliation_status <> '', function ($query) use ($affiliation_status) {
                return $query->where('producer_products.status', '=', $affiliation_status);
            })
            ->where('products.platform_id', $platform_id)
            ->where('producers.platform_user_id', $user_id)
            ->where('producers.type', 'A')
            ->where('producer_products.status', 'active')
            ->orderBy('products.name', 'ASC')
            ->paginate($offset);

        return ['error' => 'false', 'message' => 'Produdos do afiliado retornado com sucesso', 'data' => $data];
    }

    /**
     * List all affiliates on a given platform
     *
     * @param  string  $platformId
     * @param  \App\Repositories\Affiliations\Objects\AffiliationFilter  $filter
     * @return \Illuminate\Support\Collection<ProducerProduct>
     */
    public function listAllAffiliatesOnPlatform(string $platformId, AffiliationFilter $filter): Collection
    {
        return $this->listAffiliatesOnPlatformByStatus($platformId, $filter);
    }

    /**
     * List all affiliates in a give platform with "active contract"
     * @param  string  $platformId
     * @param  \App\Repositories\Affiliations\Objects\AffiliationFilter  $filter
     * @return \Illuminate\Support\Collection<ProducerProduct>
     * @deprecated Use {@see listAllAffiliatesOnPlatform()} instead
     *
     */
    public function listActiveAffiliatesOnPlatform(string $platformId, AffiliationFilter $filter): Collection
    {
        $filter->affiliationStatus = [ProducerProduct::STATUS_ACTIVE]; // ensure active contracts only
        return $this->listAffiliatesOnPlatformByStatus($platformId, $filter);
    }

    /**
     * List all affiliates in a given platform with optional "contract" status
     *
     * @param  string  $platformId
     * @param  \App\Repositories\Affiliations\Objects\AffiliationFilter|null  $filter
     * @return \Illuminate\Support\Collection<ProducerProduct>
     */
    protected function listAffiliatesOnPlatformByStatus(
        string $platformId,
        ?AffiliationFilter $filter = null
    ): Collection {
        $filter ??= AffiliationFilter::empty();

        $query = ProducerProduct::select([
            'producer_products.id AS producer_products_id',
            'producer_products.producer_id AS producer_products_producer_id',
            'producer_products.contract_limit AS producer_products_contract_limit',
            'producer_products.percent AS producer_products_percent',
            'producer_products.status AS producer_products_status',
            'producer_products.created_at AS producer_products_created_at',
            'producer_products.canceled_at AS producer_products_canceled_at',
            'producers.id AS producers_id',
            'platforms_users.id AS platform_users_id',
            'platforms_users.name AS platform_users_name',
            'platforms_users.email AS platform_users_email',
            'products.id AS products_id',
            'products.name AS products_name',
        ])
            ->join('producers', 'producers.id', '=', 'producer_products.producer_id')
            ->join('platforms_users', 'platforms_users.id', '=', 'producers.platform_user_id')
            ->join('products', 'products.id', '=', 'producer_products.product_id')
            ->where('producers.type', Producer::TYPE_AFFILIATE)
            ->where('products.platform_id', $platformId)
            ->where(function ($q) {
                // skip blocked contracts with affiliate not blocked $isContractBlocked && !$isProducerBlocked
                $q->whereNull('producers.blocked_at')
                    ->orWhere('producer_products.status', '<>', ProducerProduct::STATUS_BLOCKED);
            })
            ->when($filter->affiliationStatus, function ($query, $affiliationStatus) {
                $query->whereIn('producer_products.status', $affiliationStatus);
            })
            ->when($filter->productName, function ($query, $productName) {
                $query->where('products.name', 'like', '%'.$productName.'%');
            })
            ->when($filter->createdPeriod, function ($query, PeriodFilter $period) {
                $query->whereBetween('producer_products.created_at', [$period->startDate, $period->endDate]);
            })
            ->when($filter->products, function ($query, $productsIds) {
                $query->whereIn('products.id', $productsIds);
            })
            ->when($filter->names, function ($query, $names) {
                $query->whereIn('platforms_users.id', $names);
            })
            ->when($filter->emails, function ($query, $emails) {
                $query->whereIn('platforms_users.id', $emails);
            })
            ->when($filter->search, function ($query, $searchTerm) {
                $query->where(function ($query) use ($searchTerm) {
                    $query->orWhere('platforms_users.name', 'like', '%'.$searchTerm.'%')
                        ->orWhere('platforms_users.email', 'like', '%'.$searchTerm.'%')
                        ->orWhere('products.name', 'like', '%'.$searchTerm.'%')
                        ->orWhere('producers.id', '=', $searchTerm);
                });
            })
            ->orderBy('producer_products.created_at', 'DESC');

        $contracts = $query->get();

        return $contracts;
    }

    public function listLinksOfAffiliate(int $product_id)
    {
        $product = Product::find($product_id);
        $platformId = $product->platform_id;

        $affiliate = Producer::select('id')
            ->where('type', 'A')
            ->where('platform_id', $platformId)
            ->where('platform_user_id', Auth::user()->id)
            ->first();

        $plans = Plan::select('id', 'name', 'platform_id')
            ->where('product_id', $product_id)
            ->where('status', 1)
            ->get();

        $links = [];

        foreach ($plans as $plan) {
            $links[] = [
                'id' => $plan->id,
                'name' => $plan->name,
                'link' => $this->generatePlanLink($plan->id, $plan->platform_id, $affiliate->id)
            ];
        }

        $product_links = ProductLink::select('id', 'link_name', 'url', 'script_code', 'plan_id')
            ->where('product_id', $product_id)
            ->get();

        $additional_link = [];

        foreach ($product_links as $link) {
            $additional_link[] = [
                'id' => $link->id,
                'link_name' => $link->link_name,
                'url' => $this->generateShortProductsLink($link->plan_id, $platformId, $affiliate->id, $link->url),
                'script_code' => $link->script_code
            ];
        }

        $instructions = AffiliationSettings::select('id', 'instructions', 'support_email')
            ->where('product_id', $product_id)
            ->first()
            ->toArray();

        return [
            'links' => $links,
            'additional_links' => $additional_link,
            'instructions' => $instructions
        ];
    }

    public function generatePlanLink($plan_id, $platform_id, $affiliate_id)
    {
        $uri = config('app.url_checkout').'/'.$platform_id.'/'.base64_encode($plan_id).'/?xa='.$affiliate_id;
        $redirectUri = config('app.url_checkout').'/'.$platform_id.'/'.base64_encode($plan_id);
        $shortLink = $this->urlShortenerService->getShortLink($platform_id, $plan_id, $affiliate_id, $redirectUri);

        return $shortLink;
    }

    public function generateShortProductsLink($plan_id, $platform_id, $affiliate_id, $redirectUri)
    {
        $shortLink = $this->urlShortenerService->getShortLink($platform_id, $plan_id, $affiliate_id, $redirectUri);

        return $shortLink;
    }

    /**
     * Change producer status to "blocked" and cancel all related contracts
     *
     * @param  int  $producerId
     * @return int
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function blockAffiliateAndContracts(int $producerId): int
    {
        return DB::transaction(function () use ($producerId) {
            $affiliate = Producer::findOrFail($producerId);

            $affiliate->blocked_at = Carbon::now();
            $affiliate->save();

            $contractsAffected = ProducerProduct::where('producer_id', $affiliate->id)
                ->whereIn('status', [
                    ProducerProduct::STATUS_ACTIVE,
                    ProducerProduct::STATUS_PENDING
                ])
                ->update([
                    'status' => ProducerProduct::STATUS_BLOCKED,
                    'canceled_at' => Carbon::now(),
                ]);

            return $contractsAffected;
        });
    }

    public function unblockAffiliate(int $producerId): bool
    {
        $affiliate = Producer::findOrFail($producerId);

        $affiliate->blocked_at = null;
        return $affiliate->save();
    }

    public function affiliateFilters(string $platformId): array
    {
        $products = Producer::select('products.id as value', 'products.name as label')
            ->join('producer_products', 'producer_products.producer_id', 'producers.id')
            ->join('products', 'products.id', 'producer_products.product_id')
            ->where('producers.platform_id', $platformId)
            ->where('producers.type', 'A')
            ->groupBy('products.id', 'products.name')
            ->get()
            ->toArray();

        return [
            'products' => $products
        ];
    }

    public function getDetailOfAffiliate($platformId, $producerProductId): array
    {
        return Producer::select('producer_products.status', 'producer_products.created_at', 'producer_products.canceled_at', 'producer_products.percent', 'products.name as product_name','platforms_users.name', 'platforms_users.email', 'clients.phone_number', 'clients.cpf', 'clients.cnpj', 'clients.address', 'clients.number', 'clients.complement', 'clients.district', 'clients.city', 'clients.state', 'clients.zipcode')
            ->join('platforms_users', 'platforms_users.id', 'producers.platform_user_id')
            ->leftjoin('clients', 'clients.email', 'platforms_users.email')
            ->join('producer_products', 'producer_products.producer_id', 'producers.id')
            ->join('products', 'products.id', 'producer_products.product_id')
            ->where('producer_products.id', $producerProductId)
            ->where('producers.platform_id', $platformId)
            ->where('producers.type', 'A')
            ->get()
            ->toArray();

    }

    public function getUserAffiliateData($platformId, $producerId): array
    {
        return Producer::select(
                'platforms_users.name',
                'platforms_users.email',
                'clients.phone_number',
                'clients.cpf',
                'clients.cnpj',
                'clients.address',
                'clients.number',
                'clients.complement',
                'clients.district',
                'clients.city',
                'clients.state',
                'clients.zipcode'
            )
            ->join('platforms_users', 'platforms_users.id', 'producers.platform_user_id')
            ->leftjoin('clients', 'clients.email', 'platforms_users.email')
            ->where('producers.platform_id', $platformId)
            ->where('producers.type', 'A')
            ->where('producers.id', $producerId)
            ->get()
            ->toArray();

    }

    public function changeAffiliateStatusById($platformId, $producerProductId, $status)
    {
        $affiliateProduct = ProducerProduct::select('producer_products.id')
            ->join('producers', 'producers.id', 'producer_products.producer_id')
            ->where('producer_products.id', $producerProductId)
            ->where('producers.platform_id', $platformId)
            ->first();

        if($affiliateProduct){
            return ProducerProduct::find($affiliateProduct->id)
                ->update(['status' => $status]);
        }

        return 'Afiliação não existe';
    }

    public function listAllAffiliatesByStatus(AffiliationFilter $filter): Collection
    {
        $filter ??= AffiliationFilter::empty();
        $userId = Auth::user()->id;

        $query = ProducerProduct::select([
            'producer_products.id AS producer_products_id',
            'producer_products.producer_id AS producer_products_producer_id',
            'producer_products.contract_limit AS producer_products_contract_limit',
            'producer_products.percent AS producer_products_percent',
            'producer_products.status AS producer_products_status',
            'producer_products.created_at AS producer_products_created_at',
            'producer_products.canceled_at AS producer_products_canceled_at',
            'producers.id AS producers_id',
            'platforms_users.id AS platform_users_id',
            'platforms_users.name AS platform_users_name',
            'platforms_users.email AS platform_users_email',
            'platforms.name AS platform_name',
            'products.id AS products_id',
            'products.name AS products_name',
            'files.filename AS files_filename',
        ])
            ->join('producers', 'producers.id', '=', 'producer_products.producer_id')
            ->join('platforms_users', 'platforms_users.id', '=', 'producers.platform_user_id')
            ->join('products', 'products.id', '=', 'producer_products.product_id')
            ->join('platforms', 'platforms.id', '=', 'producers.platform_id')
            ->leftJoin('files', 'files.id', '=', 'products.image_id')
            ->where('producers.type', 'A')
            ->where('producers.platform_user_id', $userId)
            ->when($filter->affiliationStatus, function ($query, $affiliationStatus) {
                $query->whereIn('producer_products.status', $affiliationStatus);
            })
            ->when($filter->productName, function ($query, $productName) {
                $query->where('products.name', 'like', '%'.$productName.'%');
            })
            ->when($filter->createdPeriod, function ($query, PeriodFilter $period) {
                $query->whereBetween('producer_products.created_at', [$period->startDate, $period->endDate]);
            })
            ->when($filter->products, function ($query, $productsIds) {
                $query->whereIn('products.id', $productsIds);
            })
            ->when($filter->names, function ($query, $names) {
                $query->whereIn('platforms_users.id', $names);
            })
            ->when($filter->emails, function ($query, $emails) {
                $query->whereIn('platforms_users.id', $emails);
            })
            ->when($filter->search, function ($query, $searchTerm) {
                $query->where(function ($query) use ($searchTerm) {
                    $query->orWhere('platforms_users.name', 'like', '%'.$searchTerm.'%')
                        ->orWhere('platforms_users.email', 'like', '%'.$searchTerm.'%')
                        ->orWhere('products.name', 'like', '%'.$searchTerm.'%');
                });
            })
            ->orderBy('products.name', 'ASC');

        $contracts = $query->get();
        $producerIds = $contracts->pluck('producer_products_producer_id')->unique()->toArray();
        $producers = Producer::whereIn('id', $producerIds)->get();

        return $contracts->reject(function ($contract) use ($producers) {
            $isContractBlocked = $contract->producer_products_status == ProducerProduct::STATUS_BLOCKED;
            $isProducerBlocked = $producers->find($contract->producer_products_producer_id)->blocked_at;
            return $isContractBlocked && !$isProducerBlocked;
        });
    }

}
