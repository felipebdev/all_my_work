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

/**
 * Class BigID
 *
 * @class BigID
 * @package App\SDK\BigID
 * @see https://docs.bigid.bigdatacorp.com.br/#fb287cb7-973a-4eef-9e77-f185e277b120
 */
class BigID
{
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
	private const ACCESS_TOKEN_URL = 'https://accesstoken.bigdatacorp.com.br';
	/**
	 * @var string
	 */
	private const OCR_URL = 'https://bigid.bigdatacorp.com.br';
	/**
	 * BigID constructor.
	 */
	public function __construct()
	{
		$this->_headers =
		[
			'Authorization: Bearer ' . env('BIG_ID_API_TOKEN'),
			'Content-Type: application/json'
		];
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

