<?php

namespace App\Services\Dashboard;

use App\Payment;
use Carbon\Carbon;
use stdClass;

class DashboardSalesGraph
{

    /**
     * @var Payment
     */
    private Payment $payment;

    /**
     * Interval in days
     * @var int
     */
    private int $interval = 6;

    /**
     * @var String
     */
    private String $dateStart;

    /**
     * @var String
     */
    private String $dateEnd;

    /**
     * @param Payment $payment
     */
    public function __construct(
        Payment $payment
    )
    {
        $this->payment = $payment;
    }

    /**
     * @param mixed $dateEnd
     */
    private function setDateEnd($dateEnd): void
    {
        $this->dateEnd = $dateEnd;
    }

    /**
     * @return mixed
     */
    private function getDateEnd()
    {
        return $this->dateEnd;
    }

    /**
     * @param mixed $dateStart
     */
    private function setDateStart($dateStart): void
    {
        $this->dateStart = $dateStart;
    }

    /**
     * @return mixed
     */
    private function getDateStart()
    {
        return $this->dateStart;
    }

    /**
     * Get Sales Summary
     * @param null|string dateStart
     * @param null|string dateEnd
     * @return array
     */
    public function getInfo(?string $dateStart, ?string $dateEnd): array
    {
        $dateEnd = $dateEnd ?? Carbon::now()->format('Y-m-d');
        $dateStart = $dateStart ?? Carbon::createFromDate($dateEnd)->subDays(30)->format('Y-m-d');

        $this->setDateEnd($dateEnd);
        $this->setDateStart($dateStart);

        return $this->getGraph();
    }

    /**
     * Returns vuechart plugin default deal sales data
     * @return array
     */
    private function getGraph(): array
    {
        $diffInterval = $this->getDiffInSeconds() / $this->interval;
        $dates = $this->getIntervalDates($diffInterval);

        $uniqueDates = collect($dates)->flatten()->unique()->values();
        $data['date'] = $dates;


        $data['label'] = $this->getLabel($uniqueDates);

        $datasets[0] = new stdClass();

        $datasets[0]->label = "Sales Total";
        $datasets[0]->data = $this->getData($dates);
        $datasets[0]->backgroundColor = '#f87979';

        $data['datasets'] = $datasets;

        return $data;
    }

    private function getLabel($dates)
    {
        return $dates->map(fn($date) => Carbon::createFromDate($date)->format('d/m'));
    }

    /**
     * returns difference in seconds between start date and end date
     * @return int
     */
    private function getDiffInSeconds(){
        $date1 = Carbon::createFromFormat('Y-m-d', $this->getDateStart());
        $date2 = Carbon::createFromFormat('Y-m-d', $this->getDateEnd());
        $diff = $date1->diffInSeconds($date2);
        return $diff;
    }

    /**
     * returns difference in days between start date and end date
     * @return int
     */
    private function getDiffInDays(): int
    {
        $date1 = Carbon::createFromFormat('Y-m-d', $this->getDateStart());
        $date2 = Carbon::createFromFormat('Y-m-d', $this->getDateEnd());
        $diff = $date1->diffInDays($date2);
        return $diff;
    }

    /**
     * returns dates between start and end date according to past range
     * @param float $interval interval in seconds
     * @return array
     */
    private function getIntervalDates(float $interval): array
    {
        $start = $initialDate = Carbon::createFromDate($this->getDateStart());
        $dates[] = [$start->format('Y-m-d')];

        for($i = 0; $i <= $this->interval; $i++){
            $end = Carbon::createFromDate($initialDate)->addSeconds($i * $interval);
            if($end->format('Y-m-d') != $start->format('Y-m-d')){
                $dates[] = [
                    $start->format('Y-m-d'),
                    $end->format('Y-m-d')
                ];
            }
            $start = $end;
        }

        //Remove duplicate values from a multi-dimensional array
        /*
        $dates = array_map("unserialize", array_unique(array_map("serialize", $dates)));

        return array_values($dates);
        */
        return $dates;
    }

    /**
     * Get total sales for each past interval
     * @param array $intervals
     * @return array
     */
    private function getData(array $intervals): array
    {
        $data = [];
        foreach($intervals as $interval){
            $sizeOfInterval = sizeof($interval);
            $payment = $this->payment
                            ->selectRaw('sum(customer_value) total')
                            ->where('status', 'paid')
                            ->when($sizeOfInterval === 1, function($q) use($interval){
                                return $q->whereDate('payment_date', $interval[0]);
                            })
                            ->when($sizeOfInterval === 2, function($q) use($interval){
                                return $q->where('payment_date', '>', $interval[0])
                                         ->where('payment_date', '<=', $interval[1]);
                            })
                            ->first();
            $data[] = number_format((float) $payment['total'], 2, '.', '');
        }
        return $data;
    }


}
