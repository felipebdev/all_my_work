<?php

namespace App\Services\Platform;

use App\Repositories\PaymentRepository;
use App\Repositories\SubscriberRepository;
use App\Repositories\PlatformRepository;
use App\Services\Objects\PlatformFilter;
use App\Services\Objects\PaymentFilter;
use App\Services\Objects\SubscriberFilter;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class PlatformService{

    protected PlatformRepository $platform;
    private PaymentRepository $payment;
    private SubscriberRepository $subscriber;

    public function __construct(
        PlatformRepository $platform,
        PaymentRepository $payment,
        SubscriberRepository $subscriber
    )
    {
        $this->platform = $platform;
        $this->payment = $payment;
        $this->subscriber = $subscriber;
    }

    /**
     * @param array $inputs
     * @return mixed
     */
    public function getPlatforms(array $inputs){

        $search = $inputs['search'] ?? null;

         $filter = (new PlatformFilter())
             ->setSearch($search);

        return $this->platform->listPlatformClient($filter)->select(
            'platforms.id',
            'platforms.created_at',
            'platforms.updated_at',
            'platforms.name',
            'platforms.url',
            'clients.company_name',
            'clients.id as client_id',
            DB::raw('CONCAT(clients.first_name," ",clients.last_name) as customer_name')
        )
        ->orderBy('platforms.updated_at', 'DESC')
        ->get();
    }

    /**
     * @param array $inputs
     * @return mixed
     */
    public function list(array $inputs){

        $search = $inputs['search'] ?? null;

         $filter = (new PlatformFilter())
             ->setSearch($search);


        return $this->platform->listAll($filter)->select(
            'platforms.id',
            'platforms.name'
        )
        ->orderBy('platforms.name', 'ASC')
        ->get();
    }

    public function getProducts(string $id)
    {
        $filter = (new PlatformFilter())
            ->setPlatformId($id);

        return $this->platform->listPlatformProductsAndPlans($filter)
                                ->select(
                                'products.id as product_id',
                                'products.name as product_name',
                                'plan_categories.name as category_name',
                                DB::raw('CONCAT(clients.first_name, " ", clients.last_name) as customer_name'),
                                'platforms.name as platform_name',
                                'plans.price',
                                'products.analysis_status')
                                ->get();
    }

    /**
     * Get platform by ID
     *
     * @param string $id
     * @return mixed
     */
    public function getPlatformById(string $id)
    {
        return $this->platform->findById($id);
    }

    /**
     * Store platform
     *
     * @param array $data
     * @param UploadedFile|null $image
     * @return mixed
     */
    public function store(array $data, ?UploadedFile $image): Model
    {
        return $this->platform->create($data, $image);
    }

    /**
     * Update the platform.
     *
     * @param string $id
     * @param array $data
     * @param UploadedFile|null $image
     * @return array
     */
    public function update(string $id, array $data, ?UploadedFile $image): array
    {
        return $this->platform->update($id, $data, $image);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $id
     * @return void
     */
    public function delete(string $id)
    {
        $this->platform->delete($id);
    }

    /**
     * @param $inputs
     * @return array
     * @throws Exception
     */
    public function getPlatformSummary($inputs): array
    {

        $period = isset($inputs['period']) ? convertStringToPeriodFilter($inputs['period']) : null;

        $platformFilter = (new PlatformFilter())->setCreatedPeriod($period);
        $summary['total_platform'] = $this->platform->listAll($platformFilter)
            ->count();

        $subscriberFilter = (new SubscriberFilter())->setCreatedPeriod($period);
        $summary['total_subscriber'] = $this->subscriber->listAll($subscriberFilter)
            ->count();

        $paymentFilter = (new PaymentFilter())->setPaymentDate($period);
        $summary['total_seller'] = $this->payment->listAll($paymentFilter->setStatus('paid'))
                                                    ->sum('payments.customer_value');

        $summary['total_canceled'] = $this->payment->listAll($paymentFilter->setStatus('canceled'))
                                                    ->sum('payments.customer_value');

        $summary['total_tax'] = $this->payment->listAll($paymentFilter->setStatus('paid'))
                                                    ->sum('payments.tax_value');

        $summary['total_tax'] = round($summary['total_tax'], 2);

        return $summary;
    }

}
