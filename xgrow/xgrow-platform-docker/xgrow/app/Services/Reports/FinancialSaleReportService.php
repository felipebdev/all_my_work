<?php

namespace App\Services\Reports;

use App\Services\Report\ReportBaseService;
use GuzzleHttp\Exception\GuzzleException as GuzzleExceptionAlias;
use Illuminate\Support\Facades\Auth;

/**
 *
 */
class FinancialSaleReportService
{
    /**
     * @param string $reportName
     * @param array $request
     * @return mixed|null
     */
    public function financialExportReport(string $reportName, array $request)
    {
        if ($reportName === 'subscription') {
            $reportName = 'subscriptions';
        }

        $queryString = http_build_query($request);

        $url =  strlen($queryString) == 0
            ? "financial/$reportName/report-to-generate-file"
            : "financial/$reportName/report-to-generate-file?$queryString";

        return $this->callFinancialApi($url);
    }

    /**
     * @param $url
     * @return mixed|null
     * @throws GuzzleExceptionAlias
     */
    public function callFinancialApi($url)
    {
        $req = (new ReportBaseService())->connectionConfig(Auth::user()->platform_id, Auth::user()->id);

        $res = $req->get($url);

        return json_decode($res->getBody()->getContents()) ?? null;
    }
}
