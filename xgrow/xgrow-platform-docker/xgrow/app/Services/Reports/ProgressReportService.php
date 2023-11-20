<?php

namespace App\Services\Reports;

use App\Services\LAService;
use App\Subscriber;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProgressReportService
{

    /**
     * @return LAService
     */
    public function getLAConnection(): LAService
    {
        return new LAService(Auth::user()->platform_id, Auth::user()->id);
    }

    /** Access Log data by period and type
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

    /** Get Simple Progress report for LA API
     * @param mixed $courseId
     * @param mixed $listSubscribers
     * @return mixed
     * @throws GuzzleException
     */
    public function getSimpleContentProgress($courseId, $listSubscribers)
    {
        try {
            $url = "/reports/simple-content-progress";
            $data = [
                "subscriberIds" => $listSubscribers,
                "courseIds" => [$courseId]
            ];

            $response = $this->getLAConnection()->get($url, $data);

            return $response->data ?? [];
        } catch (Exception $e) {
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    /** Get Complete Progress report for LA API
     * @param mixed $courseId
     * @param mixed $listSubscribers
     * @return mixed
     * @throws GuzzleException
     */
    public function getCompleteContentProgress($courseId, $listSubscribers)
    {
        try {
            $url = "/reports/complete-content-progress?courseId=$courseId";
            $response = $this->getLAConnection()->post($url, $listSubscribers);
            return $response->data ?? [];
        } catch (Exception $e) {
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }


    /** Get subscriber list from progress
     * @param null $term
     * @param null $courseId
     * @return Builder
     */
    public function getSubscribersForProgess($term = null, $courseId = null): Builder
    {
        $query = Subscriber::select(DB::raw(
            "subscribers.id,
                   subscribers.name,
                   subscribers.email,
                   subscribers.main_phone as phone,
                   subscribers.cel_phone as cellphone,
                   subscribers.created_at,
                   GROUP_CONCAT(DISTINCT products.name) AS products_name,
                   CONCAT(',', GROUP_CONCAT(DISTINCT products.id), ',')  AS products"))
            ->leftJoin('subscriptions', 'subscribers.id', '=', 'subscriptions.subscriber_id')
            ->leftJoin('plans', 'subscriptions.plan_id', '=', 'plans.id')
            ->leftJoin('products', 'plans.product_id', '=', 'products.id')
            ->where('subscribers.platform_id', '=', Auth::user()->platform_id)
            ->where('subscribers.status', '!=', Subscriber::STATUS_LEAD);

        if ($term) {
            $query->where(function (Builder $q) use ($term) {
                return $q
                    ->where('subscribers.name', 'like', "%$term%")
                    ->orWhere('subscribers.email', 'like', "%$term%");
            });
        }

        if ($courseId) {
            $query->leftJoin('course_product', 'course_product.product_id', '=', 'products.id');
            $query->where('course_product.course_id', '=', $courseId);
        }

        return $query->groupBy('subscribers.id')
            ->orderBy('subscribers.created_at', 'DESC');
    }
}
