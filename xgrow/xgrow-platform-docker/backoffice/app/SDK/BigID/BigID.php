<?php
/**
 * Copyright (C) TheOne / ONI - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Tiago Souza <tiagodjf@gmail.com>
 * If you purchased this software, see the license.txt file contained in this source code for more information and possible exceptions.
 */
declare(strict_types=1);

namespace App\SDK\BigID;

use App\Net\CURL;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use \JsonException;

/**
 * Class BigID
 *
 * @class BigID
 * @package App\SDK\BigID
 * @see https://docs.bigid.bigdatacorp.com.br/#fb287cb7-973a-4eef-9e77-f185e277b120
 */
class BigID
{
	const MESSAGES =
	[
		'backgroundCheck' =>
		[
			'90' => '%s aprovado',
			'-1100' => '%s não aprovado',
			'-1101' => 'Ocorreu um erro ao verificar o %s',
			'-1102' => 'O %s não possui informações',
			'-1' => 'Ocorreu um erro ao verificar o %s, tente novamente após alguns segundos'
		]
	];
	/**
	 * @var string
	 */
	const CNH = 'CNH';
	/**
	 * @var string
	 */
	const RG = 'RG';
	/**
	 * @var string
	 */
	const NEWRG = 'NEWRG';
	/**
	 * @var string
	 */
	const RNE = 'RNE';
	/**
	 * @var string
	 */
	const CARTAO_CPF = 'CARTAOCPF';
	/**
	 * @var string
	 */
	const RG_FRONT = 'A';
	/**
	 * @var string
	 */
	const RG_BACK = 'B';
	/**
	 * @var string
	 */
	const RG_OPEN = 'C';
	/**
	 * @var string
	 */
	const CNH_FRONT = 'A';
	/**
	 * @var string
	 */
	const CNH_BACK = 'B';
	/**
	 * @var string
	 */
	const CNH_OPEN = 'C';
	/**
	 * @var string
	 */
	const CPF = 'CPF';
	/**
	 * @var string
	 */
	const CNPJ = 'CNPJ';
	/**
	 * @var array
	 */
	protected const DOCUMENT_TYPES =
	[
		'CNH' => 'CNH',
		'RG' => 'RG',
		'NEWRG' => 'NEWRG',
		'RNE' => 'RNE',
		'CARTAO_CPF' => 'CARTAOCPF'
	];
	/**
	 * @var array
	 */
	private array $_headers = [];
	/**
	 * @var string
	 */
	private const ACCESS_TOKEN_URL = 'https://accesstoken.bigdatacorp.com.br/Generate';
	/**
	 * @var string
	 */
	private const OCR_URL = 'https://bigid.bigdatacorp.com.br';
	/**
	 * @var string
	 */
	private const QUESTIONS_URL = 'https://bigid.bigdatacorp.com.br/Questions';
	/**
	 * @var string
	 */
	private const EVENTS_URL = 'https://bigid.bigdatacorp.com.br/Events';
	/**
	 * @var string
	 */
	private const BACKGROUND_CHECK_URL = 'https://bigid.bigdatacorp.com.br/backgroundcheck';
	/**
	 * @var string
	 */
	private ?string $_login = null;
	/**
	 * @var string
	 */
	private ?string $_password = null;
	/**
	 * @var string|false|mixed|null
	 */
	private ?string $_token = null;
	/**
	 * @var string|null
	 */
	private ?string $_tokenID = null;
	/**
	 * @var string|null
	 */
	private ?string $_tokenExpiration = null;
	/**
	 * BigID constructor.
	 */
	public function __construct()
	{
		$this->_login = env('BIG_ID_USER');
		$this->_password = env('BIG_ID_PASSWORD');

		$this->_headers =
		[
			'Authorization: Bearer ' . env('BIG_ID_API_TOKEN'),
			'Content-Type: application/json',
			'Login' => $this->_login,
			'Password' => $this->_password
		];

		//$this->_token = apcu_fetch('BIG_ID_TOKEN') ?? null;
		//$this->_tokenID = apcu_fetch('BIG_ID_TOKEN_ID') ?? null;
		//$this->_tokenExpiration = apcu_fetch('BIG_ID_TOKEN_EXPIRATION') ?? null;
	}
	public static function isSupported():bool
	{
		return env('BIG_ID_USER') !== null && env('BIG_ID_PASSWORD') !== null && env('BIG_ID_API_TOKEN') !== null;
	}
	/**
	 * @param string $type
	 *
	 * @return bool
	 */
	public static function documentTypeExists(string $type):bool
	{
		return in_array(strtoupper($type), self::DOCUMENT_TYPES, true);
	}
	public function accessToken()
	{
		return CURL::post
		(
			self::ACCESS_TOKEN_URL,
			[
				'login' => $this->_login,
				'password' => $this->_password,
				'expires' => 87500
			],
			$this->_headers
		);
	}
	/**
	 * @param string $cpf
	 *
	 * @return mixed
	 * @throws \JsonException
	 */
	public function cpf(string $cpf)
	{
		return CURL::post(self::QUESTIONS_URL, ['Parameters' => ['CPF=' . $cpf]], $this->_headers);
	}
	/**
	 * @param string $cpf
	 *
	 * @return mixed
	 * @throws \JsonException
	 */
	public function cpfEvents(string $cpf)
	{
		return CURL::post(self::EVENTS_URL, ['Parameters' => ['CPF=' . $cpf]], $this->_headers);
	}

	/**
	 * @param string $cpf
	 *
	 * @return array|null
	 * @throws \JsonException
	 */
	public function cpfBackgroundCheckInfo(string $cpf):?array
	{
		try
		{
			$result = $this->backgroundCheckCPF($cpf);
		}
		catch (\JsonException $exception)
		{
			$result = $this->backgroundCheckCPF($cpf);
		}

		if(!isset($result['ResultCode'])) $result = ['ResultCode' => '-1'];

		return ['code' => (int) $result['ResultCode'], 'message' => self::MESSAGES['backgroundCheck'][$result['ResultCode']]];
	}
	/**
	 * @param string $cnpj
	 *
	 * @return array|null
	 * @throws \JsonException
	 */
	public function cnpjBackgroundCheckInfo(string $cnpj):?array
	{
		try
		{
			$result = $this->backgroundCheckCNPJ($cnpj);
		}
		catch (\JsonException $exception)
		{
			$result = $this->backgroundCheckCNPJ($cnpj);
		}

		if(!isset($result['ResultCode'])) $result = ['ResultCode' => '-1'];

		return ['code' => (int) $result['ResultCode'], 'message' => self::MESSAGES['backgroundCheck'][$result['ResultCode']]];
	}
	/**
	 * @param string $cpf
	 *
	 * @return bool
	 */
	public function validateCPF(string $cpf):bool
	{
		try
		{
			$result = $this->backgroundCheckCPF($cpf);
			return (isset($result['ResultMessage']) && $result['ResultMessage'] === 'Approved');
		}
		catch (JsonException $exception)
		{
			return false;
		}
	}
	/**
	 * @param string $cnpj
	 *
	 * @return bool
	 */
	public function validateCNPJ(string $cnpj)
	{
		try
		{
			$result = $this->backgroundCheckCNPJ($cnpj);
			return (isset($result['ResultMessage']) && $result['ResultMessage'] === 'Approved');
		}
		catch (JsonException $exception)
		{
			return false;
		}
	}
	/**
	 * @param string $documentNumber
	 * @param string $documentType
	 *
	 * @return mixed
	 * @throws \JsonException
	 */
	public function backgroundCheck(array $parameters)
	{
		return CURL::post
		(
			self::BACKGROUND_CHECK_URL,
			[
				'Login' => $this->_login,
				'parameters' => $parameters
			],
			$this->_headers,
			[]
		);
	}
	/**
	 * @param string $cpf
	 *
	 * @return mixed
	 * @throws \JsonException
	 */
	public function backgroundCheckCPF(string $cpf)
	{
		return $this->backgroundCheck( ['CPF' => trim(str_replace(['.', '-'], ['', ''], $cpf))]);
	}
	/**
	 * @param string $cnpj
	 *
	 * @return mixed
	 * @throws \JsonException
	 */
	public function backgroundCheckCNPJ(string $cnpj)
	{
		return $this->backgroundCheck( ['CNPJ' => trim(str_replace(['.', '-', '/'], ['', '', ''], $cnpj))]);
	}
	/**
	 * @param string $documentURL
	 *
	 * @return array
	 * @throws \JsonException
	 */
	public function ocrDocument(string $documentURL):array
	{
		return CURL::post
		(
			self::OCR_URL . '/VerifyID',
			['Parameters' => ['DOC_IMG_URL=' . $documentURL]],
			$this->_headers
		);
	}

	/**
	 * @param string $documentType
	 * @param string $documentFrontURL
	 * @param string $documentBackURL
	 *
	 * @return array
	 * @throws \JsonException
	 */
	public function ocrDocumentByURLs(string $documentType, string $documentFrontURL, string $documentBackURL):array
	{
		return CURL::post
		(
			self::OCR_URL . '/VerifyID',
			[
				'Parameters' => ['DOC_TYPE=' . $documentType, 'DOC_IMG_URL_A=' . $documentFrontURL, 'DOC_IMG_URL_B=' . $documentBackURL]
			],
			$this->_headers
		);
	}

	/**
	 * @param string $documentType
	 * @param string $documentFrontPath
	 * @param string $documentBackPath
	 *
	 * @return array
	 * @throws \JsonException
	 */
	public function ocrDocumentByBase64(string $documentType, string $documentFrontPath, string $documentBackPath):array
	{
		return CURL::post
		(
			self::OCR_URL . '/VerifyID',
			[
				'Parameters' => ['DOC_TYPE=' . $documentType, 'DOC_IMG_URL_A=' . base64_encode($documentFrontPath), 'DOC_IMG_URL_B=' . base64_encode($documentBackPath)]
			],
			$this->_headers
		);
	}

	/**
	 * @param string $documentURL
	 *
	 * @return array
	 * @throws \JsonException
	 */
	public function ocrDocumentAutoDetect(string $documentURL):array
	{
		return CURL::post
		(
			self::OCR_URL . '/VerifyID',
			['Parameters' => ['DOC_IMG=' . base64_encode(file_get_contents($documentURL))]],
			$this->_headers
		);
	}
	/**
	 * @param string $documentURL
	 *
	 * @return array
	 * @throws \JsonException
	 */
	public function ocrForensicValidation(string $documentURL):array
	{
		return CURL::post
		(
			self::OCR_URL . '/VerifyID',
			[
				'ForensicValidations' => 'True',
				'Parameters' => ['DOC_IMG_URL=' . $documentURL]
			],
			$this->_headers
		);
	}
}

