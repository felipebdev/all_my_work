<?php

namespace App\Services\ReportAPI;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;


class ReportAPIService
{
    /**
     * Get all indicators for clients (global)
     * @param array $data
     * @return mixed|null
     * @throws GuzzleException
     */
    public function getGeneralClientStats(array $data = [])
    {
        try {
            $req = (new ReportBaseService())->connectionConfig();
            $res = $req->get('financial/backoffice/financial-summary', ['query' => $data]);

            Log::info('Get Transaction', [
                'uri' => 'financial/backoffice/financial-summary',
                'code' => $res->getStatusCode()
            ]);

            return json_decode($res->getBody()->getContents()) ?? null;

        } catch (Exception $e) {
            Log::error('Get Transaction', ['msg' => $e->getMessage(), 'code' => 500]);
            return null;
        }
    }

    /**
     * Get all transactions by product ID
     * @return mixed|null
     * @throws GuzzleException
     */
    public function getTransactionsByProductId($id, $query)
    {
        try {
            $req = (new ReportBaseService())->connectionConfig();
            $res = $req->get("financial/backoffice/product-transactions/$id", ['query' => $query]);

            Log::info('Get Product Transaction', [
                'uri' => "financial/backoffice/product-transactions/$id",
                'code' => $res->getStatusCode()
            ]);

            return json_decode($res->getBody()->getContents()) ?? null;

        } catch (Exception $e) {
            Log::error('Get Transaction', ['msg' => $e->getMessage(), 'code' => 500]);
            return null;
        }
    }

    /**
     * Full Transaction report connection
     * @return mixed|null
     * @throws GuzzleException
     */
    public function transactionsReport(array $data = [])
    {
        try {
            $req = (new ReportBaseService())->connectionConfig();
            $res = $req->get("financial/backoffice/client-transactions", ['query' => $data]);

            Log::info('Get Transaction Report', [
                'uri' => "financial/backoffice/client-transactions",
                'code' => $res->getStatusCode()
            ]);

            return json_decode($res->getBody()->getContents()) ?? null;

        } catch (Exception $e) {
            Log::error('Get Transaction Report', ['msg' => $e->getMessage(), 'code' => 500]);
            return null;
        }
    }

    /**
     * Get indicators for client
     * @param array $data
     * @param string $clientId
     * @return mixed|null
     * @throws GuzzleException
     */
    public function getClientStats(array $data, string $clientId)
    {
        try {
            $req = (new ReportBaseService())->connectionConfig();
            $res = $req->get("financial/backoffice/client-data/$clientId", ['query' => $data]);

            Log::info('Get stats for client', [
                'uri' => "financial/backoffice/client-data/$clientId",
                'code' => $res->getStatusCode()
            ]);

            return json_decode($res->getBody()->getContents()) ?? null;

        } catch (Exception $e) {
            Log::error('Get Transaction', ['msg' => $e->getMessage(), 'code' => 500]);
            return null;
        }
    }
}
