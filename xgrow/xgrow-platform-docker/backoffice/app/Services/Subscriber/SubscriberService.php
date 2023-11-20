<?php

namespace App\Services\Subscriber;

use App\Jobs\Reports\Subscribers\Models\SubscriberReport;
use App\Jobs\Reports\Subscribers\SubscribersExportCSVReportQueue;
use App\Jobs\Reports\Subscribers\SubscribersExportXLSReportQueue;

use App\Repositories\SubscriberRepository;
use App\Services\Objects\SubscriberFilter;
use App\Subscriber;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubscriberService
{

    protected SubscriberRepository $subscriber;

    public function __construct(SubscriberRepository $subscriber)
    {
        $this->subscriber = $subscriber;
    }

    /**
     * @param array $input
     * @param $offset
     * @return mixed
     * @throws Exception
     */
    public function getSubscribers(array $input, $offset)
    {

        $search = $input['search'] ?? null;
        $status = $input['status'] ?? null;
        $period = isset($input['period']) ? convertStringToPeriodFilter($input['period']) : null;
        $subscribersId = $input['subscribers'] ?? null;
        $emails = $input['emails'] ?? null;
        $documentNumber = $input['document_number'] ?? null;

        $subscriberFilter = (new SubscriberFilter())
            ->setSearch($search)
            ->setStatus($status)
            ->setCreatedPeriod($period)
            ->setSubscribersId($subscribersId)
            ->setEmails($emails)
            ->setDocumentNumber($documentNumber);

        return $this->subscriber->listAll($subscriberFilter)->select(
            'subscribers.id',
            'subscribers.name',
            'subscribers.email',
            'subscribers.cel_phone',
            'subscribers.main_phone',
            'subscribers.status',
            'subscribers.document_type',
            'subscribers.document_number',
            'subscribers.created_at',
            DB::raw("GROUP_CONCAT(DISTINCT products.name) AS products_name"),
            DB::raw("GROUP_CONCAT(DISTINCT platforms.name) AS platforms_name"),
        )
            ->leftJoin('subscriptions', 'subscribers.id', '=', 'subscriptions.subscriber_id')
            ->leftJoin('platforms', 'subscribers.platform_id', '=', 'platforms.id')
            ->leftJoin('plans', 'subscriptions.plan_id', '=', 'plans.id')
            ->leftJoin('products', 'plans.product_id', '=', 'products.id')
            ->groupBy('subscribers.id')
            ->paginate($offset);
    }

    /**
     * @param array $input
     * @return array
     * @throws Exception
     */
    public function getSubscriberSummary(array $input): array
    {

        $period = isset($input['period']) ? convertStringToPeriodFilter($input['period']) : null;

        $filterTotal = (new SubscriberFilter())
            ->setCreatedPeriod($period);

        $filterByActive = (new SubscriberFilter())
            ->setCreatedPeriod($period)
            ->setStatus(Subscriber::STATUS_ACTIVE);

        $summary['total'] = $this->subscriber->listAll($filterTotal)->count();
        $summary['active'] = $this->subscriber->listAll($filterByActive)->count();
        $summary['inactive'] = $summary['total'] - $summary['active'];

        return $summary;
    }

    /**
     * @param array $input
     * @return void
     * @throws Exception
     */
    public function exportSubscriber(array $input): void
    {
        $search = $input['search'] ?? null;
        $period = isset($input['period']) ? convertStringToPeriodFilter($input['period']) : null;

        $filters = (new SubscriberFilter())
            ->setSearch($search)
            ->setCreatedPeriod($period);

        $report = new SubscriberReport();

        $type = $input['type'] ?? 'xlsx';

        switch ($type) {
            case 'csv':
                SubscribersExportCSVReportQueue::dispatch(Auth::user(), $report, $filters);
                break;
            default:
                SubscribersExportXLSReportQueue::dispatch(Auth::user(), $report, $filters);
        }

    }

    /**
     * @param array $inputs
     * @param int $offset
     * @return LengthAwarePaginator
     */
    public function list(array $inputs, int $offset)
    {
        $search = $inputs['search'] ?? null;

        $filter = (new SubscriberFilter())
            ->setSearch($search);

        return $this->subscriber->listAll($filter)->select(
            'subscribers.id',
            'subscribers.name',
            'subscribers.email',
            'subscribers.document_type',
            'subscribers.document_number'
        )
            ->orderBy('subscribers.updated_at', 'DESC')
            ->paginate($offset);
    }

    /**
     * Change subscriber status
     *
     *
     * @param int $id
     * @param string $status
     * @return false|mixed
     */
    public function changeStatus(int $id, string $status)
    {
        return $this->subscriber->changeStatus($id, $status);
    }

    /**
     * @param array $inputs
     * @param int $offset
     * @return mixed
     */
    public function getByName(array $inputs)
    {
        $search = $inputs['search'] ?? null;

        $filter = (new SubscriberFilter())
            ->setSearch($search);

        return $this->subscriber->listAll($filter)->select(
            'subscribers.id',
            'subscribers.name',
            'subscribers.email'
        )
            ->orderBy('subscribers.name', 'ASC');
    }

    /**
     * Remove the specified subscriber.
     *
     * @param string $id
     * @return void
     */
    public function delete(string $id)
    {
        $this->subscriber->delete($id);
    }

}
