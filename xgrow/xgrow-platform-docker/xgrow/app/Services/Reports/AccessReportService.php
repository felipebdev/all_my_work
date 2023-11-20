<?php

namespace App\Services\Reports;

use App\Services\LAService;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Auth;

class AccessReportService
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
            $response = $this->getLAConnection()->get("/logs?starttime={$startTime}&endtime={$endTime}&actionType={$actionType}");
            return $response ?? [];
        } catch (Exception $e) {
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    public function getTotalViewsCourseContent($startTime, $endTime)
    {
        try {
            $response = $this->getLAConnection()->get('/producer/reports/courseorcontent?starttime={$startTime}&endtime={$endTime}');
            return json_decode($response->getBody()) ?? [];
        } catch (Exception $e) {
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }
}
