<?php

namespace App\Services\Client;

use App\Jobs\Reports\Clients\ClientsExportCSVReportQueue;
use App\Jobs\Reports\Clients\ClientsExportXLSReportQueue;
use App\Jobs\Reports\Clients\Models\ClientReport;
use App\Jobs\Reports\Platforms\Models\PlatformReport;
use App\Jobs\Reports\Platforms\PlatformsExportCSVReportQueue;
use App\Jobs\Reports\Platforms\PlatformsExportXLSReportQueue;
use App\Repositories\ClientRepository;
use App\Repositories\ProductRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\PlatformRepository;
use App\Repositories\SubscriberRepository;
use App\Services\Objects\ClientFilter;
use App\Services\Objects\PaymentFilter;
use App\Services\Objects\PlatformFilter;
use App\Services\Objects\ProductFilter;
use App\Services\Objects\SubscriberFilter;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClientService
{

    protected ClientRepository $client;
    protected PaymentRepository $payment;
    protected SubscriberRepository $subscriber;
    protected ProductRepository $product;
    protected PlatformRepository $platform;

    public function __construct(
        ClientRepository $client,
        PaymentRepository $payment,
        SubscriberRepository $subscriber,
        ProductRepository $product,
        PlatformRepository $platform
    ) {
        $this->client = $client;
        $this->payment = $payment;
        $this->subscriber = $subscriber;
        $this->product = $product;
        $this->platform = $platform;
    }


    /**
     * Get client data
     * @param int $id
     * @param array|null $columns
     * @return Model
     */
    public function getClient(int $id, ?array $columns = null): Model
    {
        $columns = $columns ?? ['*'];
        return $this->client->findById($id, $columns)->load('image:id,filename');
    }

    /**
     * @param $inputs
     * @return mixed
     * @throws Exception
     */
    public function getClients($inputs)
    {

        $search = $inputs['search'] ?? null;
        $clientsId = $inputs['clientsId'] ?? null;
        $clientType = $inputs['clientType'] ?? null;
        $period = isset($inputs['period']) ? convertStringToPeriodFilter($inputs['period']) : null;

        $filter = (new ClientFilter())
            ->setSearch($search)
            ->setClientType($clientType)
            ->setCreatedPeriod($period)
            ->setClientsId($clientsId);

        return $this->client->listAll($filter)
            ->latest()
            ->get();
    }

    /**
     * @param int $clientId
     * @param array $inputs
     * @return mixed
     */
    public function getPlatforms(int $clientId, array $inputs)
    {

        $search = $inputs['search'] ?? null;

        $filter = (new PlatformFilter())
            ->setClientId($clientId)
            ->setSearch($search);

        $clients = $this->platform->listPlatformClient($filter);

        return $clients->get();
    }

    /**
     * @param int $clientId
     * @param array $inputs
     * @return mixed
     * @throws Exception
     */
    public function getProducts(int $clientId, array $inputs)
    {

        $search = $inputs['search'] ?? null;
        $platformId = $inputs['platform_id'] ?? null;
        $period = isset($inputs['period']) ? convertStringToPeriodFilter($inputs['period']) : null;

        $filter = (new ProductFilter())
            ->setSearch($search)
            ->setCreatedPeriod($period)
            ->setClientId($clientId)
            ->setPlatformId($platformId);

        $clients = $this->product->listProductClient($filter)->select(
            'products.id as product_id',
            'products.name as product_name',
            'products.description',
            'files.filename as image',
            'products.analysis_status',
            'plan_categories.name as category_name',
            'platforms.name as platform_name',
            'plans.price',
            'plans.payment_method_boleto',
            'plans.payment_method_credit_card',
            'plans.payment_method_pix',
            'plans.payment_method_multiple_cards',
            'plans.type_plan',
            'plans.installment',
            'plans.checkout_layout',
            'plans.checkout_address'
        );

        return $clients->get();
    }

    /**
     * @param int $id
     * @return Builder|Model|object|null
     */
    public function getProductById(int $id)
    {
        $filter = (new ProductFilter())
            ->setId($id);

        return $this->product->listProductClient($filter)->select(
            'products.id',
            'products.name as product_name',
            'plans.price',
            'categories.name as category_name',
            'platforms.name as platform_name',
            'plans.payment_method_boleto',
            'plans.payment_method_credit_card',
            'plans.payment_method_pix',
            'plans.payment_method_multiple_cards',
            'products.type as product_type',
            'plans.installment',
            'plans.checkout_layout',
            'plans.checkout_address',
            'products.status as product_status',
            'files.filename as product_image',
            'products.status as product_status'
        )->first();
    }

    /**
     * @param $inputs
     * @return mixed
     * @throws Exception
     */
    public function getClientSummary($inputs)
    {

        $period = isset($inputs['period']) ? convertStringToPeriodFilter($inputs['period']) : null;

        $clientFilter = (new ClientFilter())->setCreatedPeriod($period);
        $summary['total_client'] = $this->client->listAll($clientFilter)
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

    /**
     * @param $id
     * @param $inputs
     * @return mixed
     * @throws Exception
     */
    public function getIndividualClientSummary($id, $inputs)
    {

        $period = isset($inputs['period']) ? convertStringToPeriodFilter($inputs['period']) : null;

        $subscriberFilter = (new SubscriberFilter())->setClientId($id)->setCreatedPeriod($period);
        $summary['total_subscriber'] = $this->subscriber->listSubscriberClient($subscriberFilter)
            ->count('subscribers.id');


        $productFilter = (new ProductFilter())->setClientId($id)->setCreatedPeriod($period);
        $summary['total_product'] = $this->product->listProductClient($productFilter)
            ->count('products.id');

        $paymentFilter = (new PaymentFilter())->setClientId($id)->setStatus('paid')->setPaymentDate($period);
        $summary['total_seller'] = $this->payment->listByPlatform($paymentFilter)
            ->sum('payments.customer_value');

        return $summary;
    }

    /**
     * @param array $data
     * @param UploadedFile|null $image
     * @return Model
     */
    public function store(array $data, ?UploadedFile $image): Model
    {
        return $this->client->create($data, $image);
    }

    /**
     * @param int $id
     * @param array $data
     * @param UploadedFile|null $image
     * @return Model
     */
    public function update(int $id, array $data, ?UploadedFile $image): Model
    {

        return $this->client->update($id, $data, $image);
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function delete(int $id)
    {
        return $this->client->delete($id);
    }

    /**
     * @param array $input
     * @return void
     * @throws Exception
     */
    public function exportClient(array $input): void
    {
        $search = $input['search'] ?? null;
        $clientsId = $input['clientsId'] ?? null;
        $type = $input['type'] ?? 'xlsx';

        $filters = (new ClientFilter())
            ->setSearch($search)
            ->setClientsId($clientsId);

        $report = new ClientReport();

        switch ($type) {
            case 'csv':
                ClientsExportCSVReportQueue::dispatch(Auth::user(), $report, $filters);
                break;
            default:
                ClientsExportXLSReportQueue::dispatch(Auth::user(), $report, $filters);
        }
    }

    /**
     * @param int $clientId
     * @param array $input
     * @return void
     */
    public function exportClientPlatform(int $clientId, array $input): void
    {
        $search = $input['search'] ?? null;
        $type = $input['type'] ?? 'xlsx';

        $filters = (new PlatformFilter())
            ->setClientId($clientId)
            ->setSearch($search);

        $report = new PlatformReport();

        switch ($type) {
            case 'csv':
                PlatformsExportCSVReportQueue::dispatch(Auth::user(), $report, $filters);
                break;
            default:
                PlatformsExportXLSReportQueue::dispatch(Auth::user(), $report, $filters);
        }
    }


    /**
     * Get clients by name
     * @param string $name
     * @return Builder[]|Collection
     */
    public function getByName(string $name)
    {

        $search = $name ?? null;

        $filter = (new CLientFilter())
            ->setSearch($search);

        return $this->client->listAll($filter)
            ->select('clients.id', 'clients.first_name', 'clients.last_name')
            ->orderBy('clients.first_name', 'ASC')
            ->get();
    }

    /**
     * Get client list
     * @param array $inputs
     * @return mixed
     */
    public function list(array $inputs)
    {
        $search = $inputs['search'] ?? null;

        $filter = (new CLientFilter())
            ->setSearch($search);

        return $this->client->listAll($filter)->select(
            'clients.id',
            DB::RAW('IF(clients.type_person = "F" ,CONCAT(clients.first_name, " ", clients.last_name), clients.company_name) as name'),
            'clients.type_person',
            'clients.cpf',
            'clients.cnpj'
        )
            ->orderBy('name', 'ASC')
            ->get();
    }
}
