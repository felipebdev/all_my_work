<?php

namespace App\Repositories\Dashboard;
use App\Repositories\Contracts\ManagePeriodInterface;

class ManagePeriod implements ManagePeriodInterface
{

    private $start;
    private $end;
    private $platform_id;
    private $period_range;

    public function __construct($start, $end, $platform_id){
        $this->start = $start;
        $this->end = $end;
        $this->platform_id = $platform_id;
        $diffInDay = $this->getDiffInDay($start, $end);
        //If the date difference is greater than 31 days, select the chart with monthly period
        $this->period_range = ($diffInDay > 31) ? new MonthlyPeriod() : new DailyPeriod();
    }

    public function getLabel(): array
    {
        return $this->period_range->getLabel($this->start, $this->end);
    }


    public function getSalesProduct($product_id): array
    {
        return $this->period_range->getSalesProduct($this->start, $this->end, $product_id, $this->platform_id);
    }

    private function getDiffInDay($start, $end){
        $d1 = strtotime($start);
        $d2 = strtotime($end);
        $totalSecondsDiff = abs($d1-$d2);
        $totalDaysDiff    = $totalSecondsDiff/60/60/24;  
        return $totalDaysDiff;
    }

    public function getColorByIndex($index)
    {
        $colors = [
            '#A1EB31',
            '#f67019',
            '#f53794',
            '#537bc4',
            '#acc236',
            '#166a8f',
            '#00a950',
            '#58595b',
            '#8549ba'
        ];

        return $colors[$index % count($colors)];
    }
}