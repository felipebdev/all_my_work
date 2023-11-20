<?php

namespace App\Services;

use App\Services\Objects\Result;
use Exception;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use App\Platform;
use App\Plan;
use App\Producer;
use App\Payments;
use Illuminate\Support\Collection;
use App\Http\Traits\CustomResponseTrait;
use Illuminate\Support\Facades\Log;
use App\Services\LA\LaProducerBaseService;

class AffiliatesEventService
{
    use CustomResponseTrait;

    const TYPE = [
      'landingPage',
      'pageView',
      'leadRegister',
      'purchase',
      'purchaseWithOrderBump',
      'purchaseUpSell',
      'upSell',
      'oneClickBuy',
    ];

    /**
     * @var GuzzleHttp\Client
     */
    private $affiliateClient;

    /**
     * @var string
     */
    private $baseUrlAffiliate;

    /**
     * @var string
     */
    private $platformId;

    /**
     * @var
     */
    protected $headers;

    /**
     * @var string
     */
    private $bearerToken;

    /**
     * @var App\Platform
     */
    private Platform $platform;

    public function __construct(
        string $platformId,
        int $producerId
    ) {

        $this->affiliateClient = new HttpClient([
            'base_uri' => config('app.url_affiliates_api'),
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ]);
        $this->platformId = $platformId;
        $this->baseUrlAffiliate = config('app.url_affiliates_api');

        $bearerToken = (new LaProducerBaseService)->generateToken($platformId, $producerId);
        $this->bearerToken = $bearerToken;
        $this->headers = $this->setHeaders();
    }

    /**
     * @return array
     */
    private function setHeaders(): array
    {
        return [
            'Authorization' => "Bearer {$this->bearerToken}",
        ];
    }

    /**
     * @return array
     */
    private function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @return array
     */
    public function getAffiliatesEvents($query) : Result
    {
        try {
            $response = $this->affiliateClient->request(
                'GET',
                "{$this->baseUrlAffiliate}/affiliates/events",
                [
                    'headers' => $this->getHeaders(),
                    'query' => $query
                ],
            );
            Log::info("Events Data - Response API", [
                'getBody' => json_decode($response->getBody(), true),
            ]);

            if ($response->getStatusCode() != 200) {
                throw new Exception;
            }

            return $this->formatInformationEvents($response->getBody());

        } catch (Exception $e) {
            Log::error("Events Data - Response API failed", [
                'exception_message' => $e->getMessage(),
            ]);

            return Result::failed($e->getMessage());
        }
    }

    /**
     * Get Platforms
     * @return array
     */
    private function getPlatforms() : array
    {
        return Platform::where('id', $this->platformId)->pluck('name', 'id')->toArray();
    }

    /**
     * Get Plans
     * @return array
     */
    public function getPlans() : array
    {
        return Plan::where('platform_id', $this->platformId)->pluck('name', 'id')->toArray();
    }

    /**
     * Get Users
     * @param  array  $ids
     * @return array
     */
    public function getUsersOfAffiliates(array $ids) : array
    {
        return Producer::select('platforms_users.name', 'producers.id')
            ->join('platforms_users', 'platforms_users.id', '=', 'producers.platform_user_id')
            //->where('producers.platform_id', $this->platformId)
            //->where('producers.type', 'A')
            ->whereIn('producers.id', $ids)
            ->pluck('platforms_users.name', 'producers.id')->toArray();
    }

    /**
     * @return array
     */
    private function formatInformationEvents($obj): Result
    {
        $return = [];
        $response_formated = collect(json_decode($obj));

        $data = $response_formated['data'] ?? [];

        if (!$data) {
            return Result::ok('Nenhum registro encontrado!', []);
        }

        $affiliatesIds = $this->getAffiliatesIds($data);

        $platform = $this->getPlatforms();
        $plan = $this->getPlans();
        $user = $this->getUsersOfAffiliates($affiliatesIds);

        foreach ($data as $k => $res) {
            $return[$k]['id'] = $res->_id;
            $return[$k]['type'] = $res->type;
            $return[$k]['source'] = $res->source;
            $return[$k]['created_at'] = $res->created_at;
            $return[$k]['buyer_id'] = $res->buyerId;
            $return[$k]['affiliate_id'] = $res->affiliateId ?? '';
            $return[$k]['affiliate_name'] = $user[$res->affiliateId] ?? '';
            $return[$k]['platform_id'] = $res->platformId;
            $return[$k]['platform_name'] = $platform[$res->platformId] ?? '';
            $return[$k]['plan_id'] = $res->planId;
            $return[$k]['plan_name'] = $plan[$res->planId] ?? '';
            $return[$k]['ip'] = $res->userIp;
            $return[$k]['order_number'] = $res->orderNumber ?? '';
        }

        return Result::ok('Sucesso', [
            'events' => $return,
            'current_page' => $response_formated['current_page'],
            'per_page' => $response_formated['per_page'],
            'total' => $response_formated['total'],
            'total_pages' => $response_formated['total_pages']
        ]);
    }

    private function getAffiliatesIds(array $events) : array
    {
        $ids = [];

        foreach ($events as $data) {
            $ids[] = $data->affiliateId;
        }

        return $ids;
    }

}
