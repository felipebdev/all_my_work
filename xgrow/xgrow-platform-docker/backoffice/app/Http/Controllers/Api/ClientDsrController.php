<?php

namespace App\Http\Controllers\Api;

use App\Helpers\CollectionHelper;
use App\Http\Controllers\Controller;
use App\Http\Traits\CustomResponseTrait;
use App\Subscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientDsrController extends Controller
{
    use CustomResponseTrait;
    /**
     * @var string
     */
    public const STATUS =
        [
            'paid' => 'pago',
            'pending' => 'pendente',
            'canceled' => 'cancelado',
            'failed' => 'falhou',
            'chargeback' => 'charge back',
            'expired' => 'expirado'
        ];
    /**
     * @var string
     */
    public const TABLE_COLUMNS =
        [
            [
                'name' => 'subscriber_name',
                'label' => 'Nome',
                'type' => 'name',
                'typeData' => null,
                'owner' => 'student'
            ],
            [
                'name' => 'subscriber_email',
                'label' => 'Email',
                'type' => 'email',
                'typeData' => null,
                'owner' => 'student'
            ],
            [
                'name' => 'subscriber_document_number',
                'label' => 'CPF ou CNPJ',
                'type' => 'document',
                'typeData' => null,
                'owner' => 'student'
            ],
            [
                'name' => 'subscriber_last_access',
                'label' => 'Último login',
                'type' => 'date',
                'typeData' => null,
                'owner' => 'student'
            ],
            [
                'name' => 'test_button',
                'label' => '',
                'type' => '',
                'typeData' => null,
                'owner' => 'student',
            ],
        ];

    private $subscriber;

    public function __construct(Subscriber $subscriber)
    {
        $this->subscriber = $subscriber;
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $owners = array_column(self::TABLE_COLUMNS, 'owner');

            $totalOwnerColumns = ['student' => 0];

            array_walk($owners, static function (string $owner) use (&$totalOwnerColumns)
            {
                ++$totalOwnerColumns[$owner];
            });

            return $this->customJsonResponse(
                'Dados carregados com sucesso.',
                200,
                [
                    'columns' => self::TABLE_COLUMNS,
                    'totalColumns' => $totalOwnerColumns,
                    'status' => self::STATUS
                ]
            );

        } catch (\Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param bool $onlyMeta
     *
     * @return array|false|string
     * @throws \JsonException
     */
    private function parse(Request $request, bool $onlyMeta = false)
    {
        set_time_limit(0);

        $queryString = $request->query();
        $order = ($queryString['order'] ?? null);
        $draw = (int)($queryString['draw'] ?? 1);
        $itemsPerPage = 0;
        $page = $draw + 1;
        $search = $queryString['search'] ?? null;
        $resultType = $queryString['result_type'] ?? 'associative';

        $subscriber_name = $queryString['subscriber_name'] ?? '';
        $subscriber_email = $queryString['subscriber_email'] ?? '';
        $subscriber_document_number = $queryString['subscriber_document_number'] ?? '';
        $subscriber_last_access = $queryString['subscriber_last_access'] ?? '';

        $query = $this->subscriber
            ->select
            (
                'subscribers.*',
                'subscribers.name as subscriber_name',
                'subscribers.email as subscriber_email',
                'subscribers.document_number as subscriber_document_number',
                'subscribers.last_acess as subscriber_last_access'
            );

        $countTotal = $query->count();

        if ($subscriber_name) $query = $query->where('subscribers.name', 'like', "%{$subscriber_name}%");

        if ($subscriber_email) $query = $query->where('subscribers.email', 'like', "%{$subscriber_email}%");

        if ($subscriber_document_number) $query = $query->where('subscribers.document_number', 'like', "%{$subscriber_document_number}%");

        if ($subscriber_last_access) $query = $query->where('subscribers.last_acess', 'like', "%{$subscriber_last_access}%");

        $countFiltered = $query->count();

        $columnsNames = array_column(self::TABLE_COLUMNS, 'name');

        if (isset($order[0]['column'], $order[0]['dir'])) foreach ($order as $columnOrder) $query = $query->orderBy($columnsNames[$columnOrder['column']], $columnOrder['dir']);

        return (!$onlyMeta ? [$query, $queryString, $itemsPerPage, $draw, $countTotal, $countFiltered, $resultType] : [$countTotal, $countFiltered]);
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return array|false|string
     * @throws \JsonException
     */
    public function report(Request $request)
    {
        try {

            $return = $this->subscriber->FindOrFail($request->input('subscriber_id'));

            return $this->customJsonResponse(
                'Dados carregados com sucesso.',
                200,
                [
                    'subscriber' => $return
                ]
            );

        } catch (\Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }

    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return false|string
     * @throws \JsonException
     */
    public function info(Request $request)
    {
        [$countTotal, $countFiltered] = $this->parse($request, true);
        return json_encode
        (
            [
                'total' => $countTotal,
                'totalFiltered' => $countFiltered
            ],
            JSON_THROW_ON_ERROR
        );
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return false|string
     * @throws \JsonException
     */
    public function read(Request $request)
    {
        try {

            $offset = $request->input('offset') ?? 25;

            [$query, $queryString, $itemsPerPage, $draw, $countTotal, $countFiltered, $resultType] = $this->parse($request, false);

            $rows = $query->get();

            $data = [];

            if ($resultType === 'values') {
                foreach ($rows as $row) {
                    $data[] =
                        (object)[
                            $row->id,
                            $row->subscriber_name,
                            $row->subscriber_email,
                            $row->subscriber_document_number,
                            $row->subscriber_last_access,
                            ''
                        ];
                }
            } else {
                foreach ($rows as $row) {
                    $data[] =
                        (object)[
                            'subscriber_id' => $row->id,
                            'subscriber_name' => $row->subscriber_name,
                            'subscriber_email' => $row->subscriber_email,
                            'subscriber_document_number' => $row->subscriber_document_number,
                            'subscriber_last_access' => $row->subscriber_last_access
                        ];
                }
            }


            return $this->customJsonResponse(
                'Dados carregados com sucesso.',
                200,
                [
                    'data' => CollectionHelper::paginate(collect($data), $offset)
                ]
            );

        } catch (\Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }
}
