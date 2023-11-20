<?php

namespace App\Http\Controllers;

use App\Plan;
use App\Client;
use App\Platform;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientTransactionsController extends Controller
{
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
				'name' => 'client_cpf',
				'label' => 'CPF',
				'type' => 'cpf',
				'typeData' => null,
				'owner' => 'client'
			],
			[
				'name' => 'client_cnpj',
				'label' => 'CNPJ',
				'type' => 'cnpj',
				'typeData' => null,
				'owner' => 'client'
			],
			[
				'name' => 'client_full_name',
				'label' => 'Cliente',
				'type' => 'name',
				'typeData' => null,
				'owner' => 'client'
			],
			[
				'name' => 'client_platform',
				'label' => 'Plataforma',
				'type' => 'list',
				'typeData' => null,
				'owner' => 'client'
			],
			[
				'name' => 'client_product',
				'label' => 'Produtos',
				'type' => 'list',
				'typeData' => null, 'owner' => 'client'
			],
			[
				'name' => 'payment_status',
				'label' => 'Status',
				'type' => 'object',
				'typeData' => self::STATUS,
				'owner' => 'payment'
			],
			[
				'name' => 'payment_date',
				'label' => 'Data',
				'type' => 'date',
				'typeData' => null,
				'owner' => 'payment'
			],
			[
				'name' => 'payment_value',
				'label' => 'Valor pago',
				'type' => 'currency',
				'typeData' => null,
				'owner' => 'payment'
			],
			[
				'name' => 'payment_xgrow_value',
				'label' => 'Valor XGrow',
				'type' => 'currency',
				'typeData' => null,
				'owner' => 'payment'
			],
			[
				'name' => 'payment_liquid_value',
				'label' => 'Valor líquido',
				'type' => 'currency',
				'typeData' => null,
				'owner' => 'payment'
			],
			[
				'name' => 'client_tax_percentage',
				'label' => 'Taxa do cliente',
				'type' => 'float',
				'typeData' => ['decimals' => 2, 'suffix' => '%'],
				'owner' => 'payment'
			],
			[
				'name' => 'client_tax_transaction',
				'label' => 'Taxa por transação',
				'type' => 'currency',
				'typeData' => null,
				'owner' => 'payment'
			],
			[
				'name' => 'payments_installments',
				'label' => 'Parcelas',
				'type' => '',
				'typeData' => null,
				'owner' => 'payment'
			]
		];

	/**
	 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
	 */
	public function index()
	{
		$clients = DB::table('clients')->select
		(
			'clients.id',
			DB::raw('CONCAT(clients.first_name, \' \', clients.last_name) as client_full_name'),
			'clients.first_name',
			'clients.last_name'
		)->get();

		$owners = array_column(self::TABLE_COLUMNS, 'owner');

		$totalOwnerColumns = ['student' => 0, 'client' => 0, 'payment' => 0];

		array_walk($owners, static function (string $owner) use (&$totalOwnerColumns)
		{
			++$totalOwnerColumns[$owner];
		});

		return view
		(
			'client-transactions.index',
			[
				'columns' => self::TABLE_COLUMNS,
				'owners' => print_r($owners, true),
				'totalColumns' => $totalOwnerColumns,
				'platforms' => Platform::all('id', 'name'),
				'plans' => Plan::all('id', 'name'),
				'status' => self::STATUS,
				'clients' => $clients
			]
		);
	}
	/**
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return array
	 */
	public function getPlatformsAndProductsByClient(Request $request)
	{
		$clientIDs = $request->input('data');

		if(count($clientIDs))
		{
			$platforms = DB::table('platforms')->select('id', 'name as text')->whereIn('customer_id', $clientIDs)->get()->toArray();
			$plans = DB::table('plans')->select('id', 'name as text')->whereIn('platform_id', array_column($platforms, 'id'))->get()->toArray();
		}
		else
		{
			$platforms = Platform::all('id', 'name as text');
			$plans = Plan::all('id', 'name as text');
		}

		return ['clients' => $clientIDs, 'platforms' => $platforms, 'products' => $plans];
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
		$itemsPerPage = !isset($queryString['all']) ? (int)($queryString['length']) : 0;
		$page = $draw + 1;
		$search = $queryString['search'] ?? null;
		$resultType = $queryString['result_type'] ?? 'associative';

		$subscriber_name = $queryString['subscriber_name'] ?? '';
		$subscriber_email = $queryString['subscriber_email'] ?? '';
		$subscriber_document_number = $queryString['subscriber_document_number'] ?? '';
		$subscriber_last_access = $queryString['subscriber_last_access'] ?? '';
		$subscriber_credit_cards_last_four_digits = (string)($queryString['subscriber_credit_cards_last_four_digits'] ?? '');

		$client_cpf = $queryString['client_cpf'] ?? '';
		$client_cnpj = $queryString['client_cnpj'] ?? '';
		$clients_names = $queryString['clients_names'] ?? [];
		$client_platform = $queryString['client_platform'] ?? [];
		$client_product = $queryString['client_product'] ?? [];

		$payment_status = $queryString['payment_status'] ?? [];
		$payment_date = $queryString['payment_date'] ?? '';
		$payment_value = $queryString['payment_value'] ?? '';

		$query = DB::table('payments')
			->join('subscribers', 'payments.subscriber_id', '=', 'subscribers.id')
			->join('platforms', 'payments.platform_id', '=', 'platforms.id')
			->join('clients', 'platforms.customer_id', '=', 'clients.id')
			->leftJoin('payment_plan', 'payments.id', '=', 'payment_plan.payment_id')
			->leftJoin('plans', 'payment_plan.plan_id', '=', 'plans.id')
			->leftJoin('credit_cards', 'payments.subscriber_id', '=', 'credit_cards.subscriber_id')
			->select
			(
				'subscribers.name as subscriber_name',
				'subscribers.email as subscriber_email',
				'subscribers.document_number as subscriber_document_number',
				'subscribers.last_acess as subscriber_last_access',
				'credit_cards.last_four_digits as subscriber_credit_cards_last_four_digits',

				'clients.id as client_id',
				'clients.cpf as client_cpf',
				'clients.cnpj as client_cnpj',
				DB::raw('CONCAT(clients.first_name, \' \', clients.last_name) as client_full_name'),
				'clients.first_name as client_first_name',
				'clients.last_name as client_last_name',

				'platforms.name as client_platform',
				'plans.name as client_product',
				'payments.status as payment_status',
				'payments.payment_date as payment_date',
				'payments.subscriber_id as payments_subscriber_id',
				DB::raw('COALESCE(payment_plan.plan_price, payments.price) as payment_value'),
				DB::raw('COALESCE(payment_plan.tax_value, payments.tax_value) as payment_xgrow_value'),
				DB::raw('COALESCE(payment_plan.customer_value, payments.customer_value) as payment_liquid_value'),
				DB::raw('100 - clients.percent_split as client_tax_percentage'),
				'clients.tax_transaction as client_tax_transaction',
				'payments.installments as payments_installments'
			);

		$countTotal = $query->count();

		if ($subscriber_name) $query = $query->where('subscribers.name', 'like', "%{$subscriber_name}%");

		if ($subscriber_email) $query = $query->where('subscribers.email', 'like', "%{$subscriber_email}%");

		if ($subscriber_document_number) $query = $query->where('subscribers.document_number', 'like', "%{$subscriber_document_number}%");

		if ($subscriber_last_access) $query = $query->where('subscribers.last_acess', 'like', "%{$subscriber_last_access}%");

		if ($subscriber_credit_cards_last_four_digits) $query = $query->where('credit_cards.last_four_digits', '=', $subscriber_credit_cards_last_four_digits);

		if ($client_cpf) $query = $query->where('clients.cpf', 'like', "%{$client_cpf}%");

		if ($client_cnpj) $query = $query->where('clients.cnpj', 'like', "%{$client_cnpj}%");

		if (count($clients_names)) $query = $query->whereIn('clients.id', $clients_names);

		if (count($client_platform)) $query = $query->whereIn('platforms.id', $client_platform);

		if (count($client_product)) $query = $query->whereIn('plans.id', $client_product);

		if (count($payment_status)) $query = $query->whereIn('payments.status', $payment_status);

		if ($payment_date) $query = $query->where('payments.payment_date', 'like', "%{$payment_date}%");

		if ($payment_value) $query = $query->where('payments.price', 'like', "%" . $payment_value . "%");

		$countFiltered = $query->count();

		$columnsNames = array_column(self::TABLE_COLUMNS, 'name');

		if (isset($order[0]['column'], $order[0]['dir'])) foreach ($order as $columnOrder) $query = $query->orderBy($columnsNames[$columnOrder['column']], $columnOrder['dir']);

		return (!$onlyMeta ? [$query, $queryString, $itemsPerPage, $draw, $countTotal, $countFiltered, $resultType] : [$countTotal, $countFiltered]);
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
		[$query, $queryString, $itemsPerPage, $draw, $countTotal, $countFiltered, $resultType] = $this->parse($request, false);

		if (isset($queryString['test']) && $queryString['test'] === 'true')
		{
			return json_encode(array_merge
			(
				$queryString,
				[
					'data' => [],
					'draw' => 0,
					'recordsTotal' => 0,
					'recordsFiltered' => 0
				]
			), JSON_THROW_ON_ERROR);
		}

		$rows = $itemsPerPage > 0 ? $query->paginate($itemsPerPage) : $query->get();

		$data = [];

		if ($resultType === 'values')
		{
			foreach ($rows as $row)
			{
				$data[] =
					[
						$row->subscriber_name,
						$row->subscriber_email,
						$row->subscriber_document_number,
						$row->subscriber_last_access,
						$row->payment_date,
						$row->client_cpf,
						$row->client_cnpj,
						$row->client_full_name,
						$row->client_platform,
						$row->client_product,
						$row->payment_status,
						$row->payment_date,
						$row->payment_value,
						$row->payment_xgrow_value,
						$row->payment_liquid_value,
						$row->client_tax_percentage,
						$row->client_tax_transaction,
						$row->payments_installments
					];
			}
		} else
		{
			foreach ($rows as $row)
			{
				$data[] =
					[
						'subscriber_name' => $row->subscriber_name,
						'subscriber_email' => $row->subscriber_email,
						'subscriber_document_number' => $row->subscriber_document_number,
						'subscriber_last_access' => $row->subscriber_last_access,
						'payments_payment_date' => $row->payment_date,
						'client_cpf' => $row->client_cpf,
						'client_cnpj' => $row->client_cnpj,
						'client_full_name' => $row->client_full_name,
						'client_platform' => $row->client_platform,
						'client_product' => $row->client_product,
						'payment_status' => $row->payment_status,
						'payment_date' => $row->payment_date,
						'payment_value' => $row->payment_value,
						'payment_xgrow_value' => $row->payment_xgrow_value,
						'payment_liquid_value' => $row->payment_liquid_value,
						'client_tax_percentage' => $row->client_tax_percentage,
						'client_tax_transaction' => $row->client_tax_transaction,
						'payments_installments' => $row->payments_installments
					];
			}
		}

		return json_encode(array_merge
		(
			$queryString,
			[
				'data' => $data,
				'draw' => $draw,
				'recordsTotal' => $countTotal,
				'recordsFiltered' => $countFiltered
			]
		), JSON_THROW_ON_ERROR);
	}
}
