<?php

namespace App\Services\Reports;

use App\Services\LAService;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Auth;

class ResearchReportService
{

    /**
     * @return LAService
     */
    public function getLAConnection(): LAService
    {
        return new LAService(Auth::user()->platform_id, Auth::user()->id);
    }

    /**
     * Access Log data by period and type
     * @param $startTime
     * @param $endTime
     * @param $actionType
     * @return array|mixed
     * @throws GuzzleException
     */
    public function getLogs($startTime, $endTime, $actionType)
    {
        try {
            /** Mongo not filter in Backend */
            $response = $this->getLAConnection()->get("/logs?starttime={$startTime}&endtime={$endTime}&actionType={$actionType}");
            return $response->data ?? [];
        } catch (Exception $e) {
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }
}
