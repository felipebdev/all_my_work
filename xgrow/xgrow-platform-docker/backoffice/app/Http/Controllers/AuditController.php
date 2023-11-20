<?php

namespace App\Http\Controllers;

use App\Plan;
use App\Client;
use App\User;
use App\Audits;
use App\Platform;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use \stdClass;
use Carbon\Carbon;
use App\Data\ElasticSearch;

class AuditController extends Controller
{
	/**
	 * @var string
	 */
	private const SOURCE_TYPE = 'dataBase';
	/**
	 * @var \App\Data\ElasticSearch
	 */
	private ElasticSearch $_elasticSearch;

	/**
	 * AuditController constructor.
	 */
	public function __construct()
	{
		if(self::SOURCE_TYPE === 'elasticSearch') $this->_elasticSearch = new ElasticSearch(env('AUDIT_INDEX'));
	}

	/**
	 * @var string
	 */
	const DATE_TIME_FORMAT = 'Y-m-d H:i:s';
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
		'client' =>
		[
			/*
			[
				'name' => 'id',
				'label' => 'ID',
				'type' => 'number',
				'typeData' => null
			],*/
			[
				'name' => 'event',
				'label' => 'Ação',
				'type' => 'object',
				'typeData' => ['created' => 'Criação', 'updated' => 'Atualização', 'deleted' => 'Exclusão']
			],
			[
				'name' => 'first_name',
				'label' => 'Nome',
				'type' => 'name',
				'typeData' => null
			],
			[
				'name' => 'last_name',
				'label' => 'Sobrenome',
				'type' => 'name',
				'typeData' => null
			],
			[
				'name' => 'email',
				'label' => 'Email',
				'type' => 'email',
				'typeData' => null
			],
			[
				'name' => 'password',
				'label' => 'Senha',
				'type' => 'password',
				'typeData' => null
			],
			[
				'name' => 'type_person',
				'label' => 'Sexo',
				'type' => 'object',
				'typeData' => ['M' => 'Masculino', 'F' => 'feminino']
			],
			[
				'name' => 'client_cpf',
				'label' => 'CPF',
				'type' => 'cpf',
				'typeData' => null
			],
			[
				'name' => 'client_cnpj',
				'label' => 'CNPJ',
				'type' => 'cnpj',
				'typeData' => null
			],
			[
				'name' => 'check_document_status',
				'label' => 'Status do documento',
				'type' => 'object',
				'typeData' => [1 => 'Aprovado', 0 => 'Em análise']
			],
			[
				'name' => 'company_name',
				'label' => 'Nome da empresa',
				'type' => '',
				'typeData' => null
			],
			[
				'name' => 'fantasy_name',
				'label' => 'Nome fantasia',
				'type' => '',
				'typeData' => null
			],
			[
				'name' => 'company_url',
				'label' => 'URL da empresa',
				'type' => '',
				'typeData' => null
			],
			[
				'name' => 'address',
				'label' => 'Endereço',
				'type' => '',
				'typeData' => null
			],
			[
				'name' => 'number',
				'label' => 'Número',
				'type' => '',
				'typeData' => null
			],
			[
				'name' => 'complement',
				'label' => 'Complemento',
				'type' => '',
				'typeData' => null
			],
			[
				'name' => 'district',
				'label' => 'Bairro',
				'type' => '',
				'typeData' => null
			],
			[
				'name' => 'city',
				'label' => 'Cidade',
				'type' => '',
				'typeData' => null
			],
			[
				'name' => 'state',
				'label' => 'Estado',
				'type' => '',
				'typeData' => null
			],
			[
				'name' => 'percent_split',
				'label' => 'percent_split',
				'type' => '',
				'typeData' => null
			],
			[
				'name' => 'tax_transaction',
				'label' => 'tax_transaction',
				'type' => '',
				'typeData' => null
			],
			[
				'name' => 'bank',
				'label' => 'Banco',
				'type' => '',
				'typeData' => null
			],
			[
				'name' => 'branch',
				'label' => 'Agência',
				'type' => '',
				'typeData' => null
			],
			[
				'name' => 'account',
				'label' => 'Conta',
				'type' => '',
				'typeData' => null
			],
			[
				'name' => 'recipient_id',
				'label' => 'recipient_id',
				'type' => '',
				'typeData' => null
			],
			[
				'name' => 'statement_descriptor',
				'label' => 'statement_descriptor',
				'type' => '',
				'typeData' => null
			],
			[
				'name' => 'created_at',
				'label' => 'Data de criação',
				'type' => 'date',
				'typeData' => null
			],
			[
				'name' => 'updated_at',
				'label' => 'Última atualização',
				'type' => 'date',
				'typeData' => null
			],
			[
				'name' => 'url',
				'label' => 'URL auditoria',
				'type' => '',
				'typeData' => null
			],
			[
				'name' => 'ip_address',
				'label' => 'IP',
				'type' => '',
				'typeData' => null
			],
			[
				'name' => 'user_agent',
				'label' => 'Navegador',
				'type' => '',
				'typeData' => null
			]
		],
		'user' =>
		[
			[
				'name' => 'event',
				'label' => 'Ação',
				'type' => 'object',
				'typeData' => ['created' => 'Criação', 'updated' => 'Atualização', 'deleted' => 'Exclusão']
			],
			[
				'name' => 'first_name',
				'label' => 'Nome',
				'type' => 'name',
				'typeData' => null
			],
			[
				'name' => 'last_name',
				'label' => 'Sobrenome',
				'type' => 'name',
				'typeData' => null
			],
			[
				'name' => 'email',
				'label' => 'Email',
				'type' => 'email',
				'typeData' => null
			],
			[
				'name' => 'password',
				'label' => 'Senha',
				'type' => 'password',
				'typeData' => null
			],
			[
				'name' => 'remember_token',
				'label' => 'Token',
				'type' => '',
				'typeData' => null
			],
			[
				'name' => 'two_factor_enabled',
				'label' => 'Dois fatores ativado',
				'type' => '',
				'typeData' => null
			],
			[
				'name' => 'two_factor_code',
				'label' => 'Código de dois fatores',
				'type' => '',
				'typeData' => null
			],
			[
				'name' => 'two_factor_expires_at',
				'label' => 'Expiração do código de dois fatores',
				'type' => '',
				'typeData' => null
			],
			[
				'name' => 'created_at',
				'label' => 'Data de criação',
				'type' => 'date',
				'typeData' => null
			],
			[
				'name' => 'updated_at',
				'label' => 'Última atualização',
				'type' => 'date',
				'typeData' => null
			],
			[
				'name' => 'url',
				'label' => 'URL auditoria',
				'type' => '',
				'typeData' => null
			],
			[
				'name' => 'ip_address',
				'label' => 'IP',
				'type' => '',
				'typeData' => null
			],
			[
				'name' => 'user_agent',
				'label' => 'Navegador',
				'type' => '',
				'typeData' => null
			]
		],
		'platform' =>
		[
			[
				'name' => 'event',
				'label' => 'Ação',
				'type' => 'object',
				'typeData' => ['created' => 'Criação', 'updated' => 'Atualização', 'deleted' => 'Exclusão']
			],
			[
				'name' => 'name',
				'label' => 'Nome',
				'type' => 'string',
				'typeData' => null
			],
			[
				'name' => 'url',
				'label' => 'URL',
				'type' => 'url',
				'typeData' => null
			],
			[
				'name' => 'name_slug',
				'label' => 'slug',
				'type' => 'name_slug',
				'typeData' => null
			],
			[
				'name' => 'template_id',
				'label' => 'ID do template',
				'type' => 'template_id',
				'typeData' => null
			],
			[
				'name' => 'customer_id',
				'label' => 'ID do usuário',
				'type' => 'customer_id',
				'typeData' => null
			],
			[
				'name' => 'active',
				'label' => 'Ativo',
				'type' => 'bool',
				'typeData' => null
			],
			[
				'name' => 'segment',
				'label' => 'Segmento',
				'type' => 'segment',
				'typeData' => null
			],
			[
				'name' => 'url_official',
				'label' => 'URL official',
				'type' => 'url_official',
				'typeData' => null
			],
			[
				'name' => 'reply_to_email',
				'label' => 'Responder para email',
				'type' => '',
				'typeData' => null
			],
			[
				'name' => 'reply_to_name',
				'label' => 'Responder para o nome',
				'type' => '',
				'typeData' => null
			],
			[
				'name' => 'active_sales',
				'label' => 'Vendas ativas',
				'type' => '',
				'typeData' => null
			],
			[
				'name' => 'pixel_id',
				'label' => 'ID do pixel',
				'type' => '',
				'typeData' => null
			],
			[
				'name' => 'template_schema',
				'label' => 'Esquema do template',
				'type' => '',
				'typeData' => null
			],
			[
				'name' => 'google_tag_id',
				'label' => 'Google tag ID',
				'type' => '',
				'typeData' => null
			],
			[
				'name' => 'restrict_ips',
				'label' => 'IPs restritos',
				'type' => '',
				'typeData' => null
			],
			[
				'name' => 'ips_available',
				'label' => 'IPs disponíveis',
				'type' => '',
				'typeData' => null
			],
			[
				'name' => 'recipient_id',
				'label' => 'ID recipiente',
				'type' => '',
				'typeData' => null
			],
			[
				'name' => 'thumb_id',
				'label' => 'ID miniatura',
				'type' => '',
				'typeData' => null
			],
			[
				'name' => 'created_at',
				'label' => 'Data de criação',
				'type' => 'date',
				'typeData' => null
			],
			[
				'name' => 'updated_at',
				'label' => 'Última atualização',
				'type' => 'updated_at',
				'typeData' => null
			],
			[
				'name' => 'deleted_at',
				'label' => 'Data de exclusão',
				'type' => 'deleted_at',
				'typeData' => null
			],
			[
				'name' => 'url',
				'label' => 'URL auditoria',
				'type' => '',
				'typeData' => null
			],
			[
				'name' => 'ip_address',
				'label' => 'IP',
				'type' => '',
				'typeData' => null
			],
			[
				'name' => 'user_agent',
				'label' => 'Navegador',
				'type' => '',
				'typeData' => null
			]
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
			'audit.index',
			[
				'columns' => self::TABLE_COLUMNS,
				'owners' => print_r($owners, true),
				'totalColumns' => $totalOwnerColumns,
				'platforms' => Platform::all('id', 'name'),
				'plans' => Plan::all('id', 'name'),
				'status' => self::STATUS,
				'clients' => $clients,
				'dataBaseColumns' =>
				[
					'client' => 'Clientes',
					'user' => 'Usuários',
					'platform' => 'Plataformas'
				]
			]
		);
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
		$itemsPerPage = !isset($queryString['all']) ? (int)($queryString['length'] ?? 0) : 0;
		$page = (int) $draw + 1;
		$search = $queryString['search'] ?? null;
		$resultType = $queryString['result_type'] ?? 'associative';

		$client_ids = $queryString['client_ids'] ?? [];
		$date_start = $queryString['date_start'] ?? '';
		$date_end = $queryString['date_end'] ?? '';
		$table = $queryString['table'] ?? '';

		if(!$table) return [null, $queryString, $itemsPerPage, $draw, null, null, $resultType, null];

		$fieldID = '';
		$fieldCreatedAt = '';

		switch ($table)
		{
			case 'client':

				$query = Client::with('audits');

				$fieldID = 'id';
				$fieldCreatedAt = 'created_at';

				break;

			case 'user':

				$query = User::with('audits');

				$fieldID = 'id';
				$fieldCreatedAt = 'created_at';

				break;

			case 'platform':

				$query = Platform::with('audits');

				$fieldID = 'id';
				$fieldCreatedAt = 'created_at';

				break;
		}

		$countTotal = $query->count();

		if (count($client_ids)) $query = $query->whereIn($fieldID, $client_ids);
		if ($date_start && $date_end) $query = $query->whereBetween($fieldCreatedAt, [$date_start. " 00:00:00", $date_end. " 23:59:59"]);

		$countFiltered = $query->count();

		$columnsNames = array_column(self::TABLE_COLUMNS[$queryString['table']], 'name');

		if (isset($order[0]['column'], $order[0]['dir'])) foreach ($order as $columnOrder) $query = $query->orderBy($columnsNames[$columnOrder['column']], $columnOrder['dir']);

		return (!$onlyMeta ? [$query, $queryString, $itemsPerPage, $draw, $countTotal, $countFiltered, $resultType, $table, $page] : [$countTotal, $countFiltered]);
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
		if(self::SOURCE_TYPE === 'dataBase') return $this->readFromDataBase($request);
		return $this->readFromElasticSearch($request);
	}

	/**
	 * @param \Illuminate\Http\Request $request
	 *
	 * @return array|false|string
	 * @throws \JsonException
	 */
	private function readFromDataBase(Request $request)
	{
		[$query, $queryString, $itemsPerPage, $draw, $countTotal, $countFiltered, $resultType, $table, $page] = $this->parse($request, false);

		if(!$query) return [];

		$rows = $itemsPerPage > 0 ? $query->paginate($itemsPerPage) : $query->get();

		$data = [];

		$formatters = ['client' => 'formatDataBaseClientRows', 'user' => 'formatDataBaseUserRows', 'platform' => 'formatDataBasePlatformRows'];
		$formatter = $formatters[$table];


		foreach ($rows as $row)
		{
			if(isset($row->audits[0]))
			{
				$audits = $row->audits;

				foreach ($audits as $audit)
				{
					if(is_array($audit->old_values) && count($audit->old_values)) $data[] = $this->{$formatter}($row, $audit, 'old_values');
					if(is_array($audit->new_values) && count($audit->new_values)) $data[] = $this->{$formatter}($row, $audit, 'new_values');
				}
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

	private function readFromElasticSearch(Request $request)
	{
		$queryString = $request->query();
		$order = ($queryString['order'] ?? null);
		$draw = (int)($queryString['draw'] ?? 1);
		$itemsPerPage = !isset($queryString['all']) ? (int)($queryString['length'] ?? 0) : 0;
		$page = (int) $draw + 1;
		$search = $queryString['search'] ?? null;
		$resultType = $queryString['result_type'] ?? 'associative';

		$client_ids = $queryString['client_ids'] ?? [];
		$date_start = $queryString['date_start'] ?? '';
		$date_end = $queryString['date_end'] ?? '';
		$table = $queryString['table'] ?? '';

		if(!$table) return ['data' => []];

		$formatters = ['client' => 'formatElasticSearchClientRows', 'user' => 'formatElasticSearchUserRows', 'platform' => 'formatElasticSearchPlatformRows'];
		$formatter = $formatters[$table];

		$rows = $this->_elasticSearch->getAll($itemsPerPage);

		if(isset($rows['hits']['hits'])) $rows = $rows['hits']['hits'];
		$data = [];
		//$data = $rows;

		$countTotal = count($rows);
		$countFiltered = $countTotal;

		foreach ($rows as $row)
		{
			$audit = $row['_source'];
			$audit['id'] = $row['_id'];
			if(is_array($audit['old_values']) && count($audit['old_values'])) $data[] = $this->{$formatter}($row, $audit, 'old_values');
			if(is_array($audit['new_values']) && count($audit['new_values'])) $data[] = $this->{$formatter}($row, $audit, 'new_values');
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

	/**
	 * @param $row
	 * @param $audit
	 * @param $auditType
	 *
	 * @return array
	 */
	private function formatDataBaseClientRows($row, $audit, $auditType):array
	{
		$data =
		[
			'id' => $audit->id,
			'event' => $audit->event,
			'first_name' => $audit->{$auditType}['first_name'] ?? $row->first_name ?? '',
			'last_name' => $audit->{$auditType}['last_name'] ?? $row->last_name ?? '',
			'email' => $audit->{$auditType}['email'] ?? $row->email ?? '',
			'password' => $audit->{$auditType}['password'] ?? $row->password ?? '',
			'type_person' => $audit->{$auditType}['type_person'] ?? $row->type_person ?? '',
			'client_cpf' => $audit->{$auditType}['client_cpf'] ?? $row->client_cpf ?? '',
			'client_cnpj' => $audit->{$auditType}['client_cnpj'] ?? $row->client_cnpj ?? '',
			'check_document_status' => $audit->{$auditType}['check_document_status'] ?? $row->check_document_status ?? '',
			'company_name' => $audit->{$auditType}['company_name'] ?? $row->company_name ?? '',
			'fantasy_name' => $audit->{$auditType}['fantasy_name'] ?? $row->fantasy_name ?? '',
			'company_url' => $audit->{$auditType}['company_url'] ?? $row->company_url ?? '',
			'address' => $audit->{$auditType}['address'] ?? $row->address ?? '',
			'number' => $audit->{$auditType}['number'] ?? $row->number ?? '',
			'complement' => $audit->{$auditType}['complement'] ?? $row->complement ?? '',
			'district' => $audit->{$auditType}['district'] ?? $row->district ?? '',
			'city' => $audit->{$auditType}['city'] ?? $row->city ?? '',
			'state' => $audit->{$auditType}['state'] ?? $row->state ?? '',
			'percent_split' => $audit->{$auditType}['percent_split'] ?? $row->percent_split ?? '',
			'tax_transaction' => $audit->{$auditType}['tax_transaction'] ?? $row->tax_transaction ?? '',
			'bank' => $audit->{$auditType}['bank'] ?? $row->bank ?? '',
			'branch' => $audit->{$auditType}['branch'] ?? $row->branch ?? '',
			'account' => $audit->{$auditType}['account'] ?? $row->account ?? '',
			'recipient_id' => $audit->{$auditType}['recipient_id'] ?? $row->recipient_id ?? '',
			'statement_descriptor' => $audit->{$auditType}['statement_descriptor'] ?? $row->statement_descriptor ?? '',
			'url' => $audit->url,
			'ip_address' => $audit->ip_address,
			'user_agent' => $audit->user_agent,
			'created_at' => date("Y-m-d H:i:s", strtotime($row->created_at)),
			'updated_at' => date("Y-m-d H:i:s", strtotime($row->updated_at))
		];

		return $data;
	}
	private function formatDataBaseUserRows($row, $audit, $auditType):array
	{
		$data =
		[
			'id' => $audit->id,
			'event' => $audit->event,
			'name' => $audit->{$auditType}['name'] ?? $row->name ?? '',
			'email' => $audit->{$auditType}['email'] ?? $row->email ?? '',
			'password' => $audit->{$auditType}['password'] ?? $row->password ?? '',
			'remember_token' => $audit->{$auditType}['remember_token'] ?? $row->remember_token ?? '',
			'two_factor_enabled' => $audit->{$auditType}['two_factor_enabled'] ?? $row->two_factor_enabled ?? '',
			'two_factor_code' => $audit->{$auditType}['two_factor_code'] ?? $row->two_factor_code ?? '',
			'two_factor_expires_at' => $audit->{$auditType}['two_factor_expires_at'] ?? $row->two_factor_expires_at ?? '',
			'url' => $audit->url,
			'ip_address' => $audit->ip_address,
			'user_agent' => $audit->user_agent,
			'created_at' => self::formatDate($row->created_at),
			'updated_at' => self::formatDate($row->updated_at),
		];

		return $data;
	}
	private function formatDataBasePlatformRows($row, $audit, $auditType):array
	{
		$data =
		[
			'id' => $audit->id,
			'event' => $audit->event,
			'name' => $audit->{$auditType}['name'] ?? $row->name ?? '',
			'url' => $audit->{$auditType}['url'] ?? $row->url ?? '',
			'name_slug' => $audit->{$auditType}['name_slug'] ?? $row->name_slug ?? '',
			'template_id' => $audit->{$auditType}['template_id'] ?? $row->template_id ?? '',
			'customer_id' => $audit->{$auditType}['customer_id'] ?? $row->customer_id ?? '',
			'active' => $audit->{$auditType}['active'] ?? $row->active ?? '',
			'segment' => $audit->{$auditType}['segment'] ?? $row->segment ?? '',
			'url_official' => $audit->{$auditType}['url_official'] ?? $row->url_official ?? '',
			'reply_to_email' => $audit->{$auditType}['reply_to_email'] ?? $row->reply_to_email ?? '',
			'reply_to_name' => $audit->{$auditType}['reply_to_name'] ?? $row->reply_to_name ?? '',
			'active_sales' => $audit->{$auditType}['active_sales'] ?? $row->active_sales ?? '',
			'pixel_id' => $audit->{$auditType}['pixel_id'] ?? $row->pixel_id ?? '',
			'template_schema' => $audit->{$auditType}['template_schema'] ?? $row->template_schema ?? '',
			'google_tag_id' => $audit->{$auditType}['google_tag_id'] ?? $row->google_tag_id ?? '',
			'restrict_ips' => $audit->{$auditType}['restrict_ips'] ?? $row->restrict_ips ?? '',
			'ips_available' => $audit->{$auditType}['ips_available'] ?? $row->ips_available ?? '',
			'recipient_id' => $audit->{$auditType}['recipient_id'] ?? $row->recipient_id ?? '',
			'thumb_id' => $audit->{$auditType}['thumb_id'] ?? $row->thumb_id ?? '',
			/*'url' => $audit->url,*/
			'ip_address' => $audit->ip_address,
			'user_agent' => $audit->user_agent,
			'created_at' => self::formatDate($row->created_at),
			'updated_at' => self::formatDate($row->updated_at),
			'deleted_at' => self::formatDate($row->deleted_at)
		];

		return $data;
	}

	/**
	 * @param $date
	 *
	 * @return false|string
	 */
	private static function formatDate (?string $date = null)
	{
		if(!$date) return '';
		return date(self::DATE_TIME_FORMAT, strtotime($date));
	}

	private function formatElasticSearchClientRows($row, $audit, $auditType):array
	{
		$data =
			[
				'id' => $audit['id'],
				'event' => $audit['event'],
				'first_name' => $audit[$auditType]['first_name'] ?? $row['first_name'] ?? '',
				'last_name' => $audit[$auditType]['last_name'] ?? $row['last_name'] ?? '',
				'email' => $audit[$auditType]['email'] ?? $row['email'] ?? '',
				'password' => $audit[$auditType]['password'] ?? $row['password'] ?? '',
				'type_person' => $audit[$auditType]['type_person'] ?? $row['type_person'] ?? '',
				'client_cpf' => $audit[$auditType]['client_cpf'] ?? $row['client_cpf'] ?? '',
				'client_cnpj' => $audit[$auditType]['client_cnpj'] ?? $row['client_cnpj'] ?? '',
				'check_document_status' => $audit[$auditType]['check_document_status'] ?? $row['check_document_status'] ?? '',
				'company_name' => $audit[$auditType]['company_name'] ?? $row['company_name'] ?? '',
				'fantasy_name' => $audit[$auditType]['fantasy_name'] ?? $row['fantasy_name'] ?? '',
				'company_url' => $audit[$auditType]['company_url'] ?? $row['company_url'] ?? '',
				'address' => $audit[$auditType]['address'] ?? $row['address'] ?? '',
				'number' => $audit[$auditType]['number'] ?? $row['number'] ?? '',
				'complement' => $audit[$auditType]['complement'] ?? $row['complement'] ?? '',
				'district' => $audit[$auditType]['district'] ?? $row['district'] ?? '',
				'city' => $audit[$auditType]['city'] ?? $row['city'] ?? '',
				'state' => $audit[$auditType]['state'] ?? $row['state'] ?? '',
				'percent_split' => $audit[$auditType]['percent_split'] ?? $row['percent_split'] ?? '',
				'tax_transaction' => $audit[$auditType]['tax_transaction'] ?? $row['tax_transaction'] ?? '',
				'bank' => $audit[$auditType]['bank'] ?? $row['bank'] ?? '',
				'branch' => $audit[$auditType]['branch'] ?? $row['branch'] ?? '',
				'account' => $audit[$auditType]['account'] ?? $row['account'] ?? '',
				'recipient_id' => $audit[$auditType]['recipient_id'] ?? $row['recipient_id'] ?? '',
				'statement_descriptor' => $audit[$auditType]['statement_descriptor'] ?? $row['statement_descriptor'] ?? '',
				'url' => $audit['url'],
				'ip_address' => $audit['ip_address'],
				'user_agent' => $audit['user_agent'],
				'created_at' => self::formatDate($row['created_at'] ?? $audit['created_at'] ?? ''),
				'updated_at' => self::formatDate($row['updated_at'] ?? $audit['updated_at'] ?? '')
			];

		return $data;
	}

	private function formatElasticSearchUserRows($row, $audit, $auditType):array
	{
		$data =
			[
				'id' => $audit['id'],
				'event' => $audit['event'],
				'name' => $audit[$auditType]['name'] ?? $row['name'] ?? '',
				'email' => $audit[$auditType]['email'] ?? $row['email'] ?? '',
				'password' => $audit[$auditType]['password'] ?? $row['password'] ?? '',
				'remember_token' => $audit[$auditType]['remember_token'] ?? $row['remember_token'] ?? '',
				'two_factor_enabled' => $audit[$auditType]['two_factor_enabled'] ?? $row['two_factor_enabled'] ?? '',
				'two_factor_code' => $audit[$auditType]['two_factor_code'] ?? $row['two_factor_code'] ?? '',
				'two_factor_expires_at' => $audit[$auditType]['two_factor_expires_at'] ?? $row['two_factor_expires_at'] ?? '',
				'url' => $audit['url'],
				'ip_address' => $audit['ip_address'],
				'user_agent' => $audit['user_agent'],
				'created_at' => self::formatDate($row['created_at'] ?? $audit['created_at'] ?? ''),
				'updated_at' => self::formatDate($row['updated_at'] ?? $audit['updated_at'] ?? '')
			];

		return $data;
	}
	private function formatElasticSearchPlatformRows($row, $audit, $auditType):array
	{
		$data =
			[
				'id' => $audit['id'],
				'event' => $audit['event'],
				'name' => $audit[$auditType]['name'] ?? $row['name'] ?? '',
				'url' => $audit[$auditType]['url'] ?? $row['url'] ?? '',
				'name_slug' => $audit[$auditType]['name_slug'] ?? $row['name_slug'] ?? '',
				'template_id' => $audit[$auditType]['template_id'] ?? $row['template_id'] ?? '',
				'customer_id' => $audit[$auditType]['customer_id'] ?? $row['customer_id'] ?? '',
				'active' => $audit[$auditType]['active'] ?? $row['active'] ?? '',
				'segment' => $audit[$auditType]['segment'] ?? $row['segment'] ?? '',
				'url_official' => $audit[$auditType]['url_official'] ?? $row['url_official'] ?? '',
				'reply_to_email' => $audit[$auditType]['reply_to_email'] ?? $row['reply_to_email'] ?? '',
				'reply_to_name' => $audit[$auditType]['reply_to_name'] ?? $row['reply_to_name'] ?? '',
				'active_sales' => $audit[$auditType]['active_sales'] ?? $row['active_sales'] ?? '',
				'pixel_id' => $audit[$auditType]['pixel_id'] ?? $row['pixel_id'] ?? '',
				'template_schema' => $audit[$auditType]['template_schema'] ?? $row['template_schema'] ?? '',
				'google_tag_id' => $audit[$auditType]['google_tag_id'] ?? $row['google_tag_id'] ?? '',
				'restrict_ips' => $audit[$auditType]['restrict_ips'] ?? $row['restrict_ips'] ?? '',
				'ips_available' => $audit[$auditType]['ips_available'] ?? $row['ips_available'] ?? '',
				'recipient_id' => $audit[$auditType]['recipient_id'] ?? $row['recipient_id'] ?? '',
				'thumb_id' => $audit[$auditType]['thumb_id'] ?? $row['thumb_id'] ?? '',
				/*'url' => $audit['url,*/
				'ip_address' => $audit['ip_address'],
				'user_agent' => $audit['user_agent'],
				'created_at' => self::formatDate($row['created_at'] ?? $audit['created_at'] ?? ''),
				'updated_at' => self::formatDate($row['updated_at'] ?? $audit['updated_at'] ?? ''),
				'deleted_at' => self::formatDate($row['deleted_at'] ?? $audit['deleted_at'] ?? '')
			];

		return $data;
	}
}
