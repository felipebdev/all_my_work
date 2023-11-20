<?php

namespace App\Services\Dashboard;

class DashboardService
{

    private DashboardSummary $dashboardSummary;
    private DashboardSalesSummary $dashboardSalesSummary;
    private DashboardSalesGraph $dashboardSalesGraph;

    public function __construct(
        DashboardSummary $dashboardSummary,
        DashboardSalesSummary $dashboardSalesSummary,
        DashboardSalesGraph $dashboardSalesGraph
    ){
        $this->dashboardSummary = $dashboardSummary;
        $this->dashboardSalesSummary = $dashboardSalesSummary;
        $this->dashboardSalesGraph = $dashboardSalesGraph;
    }

    /**
     * Get Summary Data
     * @return array
     */
    public function getSummary(): array
    {
        return $this->dashboardSummary->getInfo();
    }

    /**
     * Get Sales Summary
     * @param null|string dateStart
     * @param null|string dateEnd
     * @return array
     */
    public function getSalesSummary(?string $dateStart, ?string $dateEnd): array
    {
        return $this->dashboardSalesSummary->getInfo($dateStart, $dateEnd);
    }

    /**
     * Get Sales Summary
     * @param null|string dateStart
     * @param null|string dateEnd
     * @return array
     */
    public function getSalesGraph(?string $dateStart, ?string $dateEnd): array
    {
        return $this->dashboardSalesGraph->getInfo($dateStart, $dateEnd);
    }

}
