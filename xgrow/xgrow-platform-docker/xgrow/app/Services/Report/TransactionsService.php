<?php

namespace App\Services\Report;

use App\Services\Checkout\CheckoutBaseService;
use Exception;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;


class TransactionsService
{
    private CheckoutBaseService $checkoutBaseService;

    public function __construct(CheckoutBaseService $checkoutBaseService)
    {
        $this->checkoutBaseService = $checkoutBaseService;
    }

    /**
     * List All Transactions
     * @param $data
     * @return mixed|null
     * @throws GuzzleException
     */
    public function getTransactions($data)
    {
        try {
            $req = (new ReportBaseService())->connectionConfig(Auth::user()->platform_id, Auth::user()->id);
            $res = $req->get('financial/transactions', ['query' => $data]);

            Log::info('Get Transaction', [
                'uri' => 'financial/transactions',
                'userId' => Auth::user()->id,
                'platformId' => Auth::user()->platform_id,
                'code' => $res->getStatusCode()
            ]);

            return json_decode($res->getBody()->getContents()) ?? null;

        } catch (Exception $e) {
            Log::error('Get Transaction', ['msg' => $e->getMessage(), 'code' => 500]);
            return null;
        }
    }

    /**
     * Get details from a specific transaction
     * @param $paymentId
     * @return mixed|null
     * @throws GuzzleException
     */
    public function getTransactionsDetails($paymentId)
    {
        try {
            $req = (new ReportBaseService())->connectionConfig(Auth::user()->platform_id, Auth::user()->id);
            $res = $req->get("financial/transactions/{$paymentId}");

            Log::info('Get Transaction Details', [
                'uri' => "financial/transactions/{$paymentId}",
                'userId' => Auth::user()->id,
                'platformId' => Auth::user()->platform_id,
                'paymentId' => $paymentId,
                'code' => $res->getStatusCode()
            ]);

            return json_decode($res->getBody()->getContents()) ?? null;

        } catch (Exception $e) {
            Log::error('Get Transaction Details', ['msg' => $e->getMessage(), 'code' => 500]);
            return null;
        }
    }

    /**
     * List All No Limit Transactions
     * @param $data
     * @return mixed|null
     * @throws GuzzleException
     */
    public function getNoLimitTransactions($data)
    {
        try {
            $req = (new ReportBaseService())->connectionConfig(Auth::user()->platform_id, Auth::user()->id);
            $res = $req->get('financial/nolimit', ['query' => $data]);

            Log::info('Get No Limit Transaction', [
                'uri' => 'financial/nolimit',
                'userId' => Auth::user()->id,
                'platformId' => Auth::user()->platform_id,
                'code' => $res->getStatusCode()
            ]);

            return json_decode($res->getBody()->getContents()) ?? null;

        } catch (Exception $e) {
            Log::error('Get No Limit Transaction', ['msg' => $e->getMessage(), 'code' => 500]);
            return null;
        }
    }

    /**
     * Get details from a specific no limit transaction
     * @param $subscriberId
     * @param $planId
     * @param $paymentOrderNumber
     * @return mixed|null
     * @throws GuzzleException
     */
    public function getNoLimitTransactionsDetails($subscriberId, $planId, $paymentOrderNumber)
    {
        try {
            $req = (new ReportBaseService())->connectionConfig(Auth::user()->platform_id, Auth::user()->id);
            $res = $req->get("financial/nolimit/{$subscriberId}/{$planId}/{$paymentOrderNumber}");

            Log::info('Get No Limit Transaction Details', [
                'uri' => "financial/nolimit/{$subscriberId}/{$planId}/{$paymentOrderNumber}",
                'userId' => Auth::user()->id,
                'platformId' => Auth::user()->platform_id,
                'subscriberId' => $subscriberId,
                'planId' => $planId,
                'paymentOrderNumber' => $paymentOrderNumber,
                'code' => $res->getStatusCode()
            ]);

            return json_decode($res->getBody()->getContents()) ?? null;

        } catch (Exception $e) {
            Log::error('Get No Limit Transaction Details', ['msg' => $e->getMessage(), 'code' => 500]);
            return null;
        }
    }

    /**
     * List All Subscriptions
     * @param $data
     * @return mixed|null
     * @throws GuzzleException
     */
    public function getSubscriptions($data)
    {
        try {
            $req = (new ReportBaseService())->connectionConfig(Auth::user()->platform_id, Auth::user()->id);
            $res = $req->get('financial/subscriptions', ['query' => $data]);

            Log::info('Get Subscriptions', [
                'uri' => 'financial/subscriptions',
                'userId' => Auth::user()->id,
                'platformId' => Auth::user()->platform_id,
                'code' => $res->getStatusCode()
            ]);

            return json_decode($res->getBody()->getContents()) ?? null;

        } catch (Exception $e) {
            Log::error('Get Subscriptions', ['msg' => $e->getMessage(), 'code' => 500]);
            return null;
        }
    }

    /**
     * Get details from a specific no limit transaction
     * @param $subscriberId
     * @param $planId
     * @param $paymentOrderNumber
     * @return mixed|null
     * @throws GuzzleException
     */
    public function getSubscriptionsDetails($subscriberId, $planId, $paymentOrderNumber)
    {
        try {
            $req = (new ReportBaseService())->connectionConfig(Auth::user()->platform_id, Auth::user()->id);
            $res = $req->get("financial/subscriptions/{$subscriberId}/{$planId}/{$paymentOrderNumber}");

            Log::info('Get Subscriptions Details', [
                'uri' => "financial/subscriptions/{$subscriberId}/{$planId}/{$paymentOrderNumber}",
                'userId' => Auth::user()->id,
                'platformId' => Auth::user()->platform_id,
                'subscriberId' => $subscriberId,
                'planId' => $planId,
                'paymentOrderNumber' => $paymentOrderNumber,
                'code' => $res->getStatusCode()
            ]);

            return json_decode($res->getBody()->getContents()) ?? null;

        } catch (Exception $e) {
            Log::error('Get Subscriptions Details', ['msg' => $e->getMessage(), 'code' => 500]);
            return null;
        }
    }

    /**
     * Retry Payment Transaction
     *
     * @param $paymentId
     * @return mixed
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws GuzzleException
     * @throws NotFoundExceptionInterface
     */
    public function retryPaymentTransaction($paymentId)
    {
        try {
            $req = $this->checkoutBaseService->connectionConfig(Auth::user()->platform_id, Auth::user()->id);
            $res = $req->post("payments/$paymentId/failed");

            Log::info('Retry Transaction payments', [
                'uri' => "payments/$paymentId/failed",
                'userId' => Auth::user()->id,
                'platformId' => Auth::user()->platform_id,
                'paymentID' => $paymentId,
                'code' => $res->getStatusCode()
            ]);

            return json_decode($res->getBody());

        } catch (ClientException $e) {
            $data = json_decode($e->getResponse()->getBody()->getContents());
            Log::error('Retry Transaction payments', ['msg' => json_encode($data), 'code' => 500]);
            throw new \Exception($data->message, 400);
        }
    }
}
