<?php
/**
 * Copyright (C) TheOne / ONI - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Tiago Souza <tiagodjf@gmail.com>
 * If you purchased this software, see the license.txt file contained in this source code for more information and possible exceptions.
 */
declare(strict_types=1);

namespace App\Data\Net;

use App\Data\Validator;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

/**
 * Class HTTPResponse
 *
 * @class HTTPResponse
 * @package App\Data\Net
 *
 * @property bool $isGET
 * @property bool $isPOST
 * @property bool $isPUT
 * @property bool $isDELETE
 * @property bool $isPATCH
 * @property bool $isHEAD
 * @property bool $isOPTIONS
 * @property Model $model
 * @property Request $request
 * @property int $status
 * @property string $statusMessage
 */
class HTTPResponse extends HTTP
{
	/**
	 * @var int
	 */
	public int $status = -1;
	/**
	 * @var string
	 */
	private string $statusMessage = '';
	/**
	 * @var array
	 */
	public ?array $data = null;
	/**
	 * @var array|null
	 */
	public ?array $messages = null;
	/**
	 * @var array
	 */
	public ?array $rules = null;
	/**
	 * @var array
	 */
	private array $_validation = [];
	/**
	 * @var bool
	 */
	private bool $_validationSuccess = true;
	/**
	 * @constructor
	 */
	public function __construct()
	{
		parent::__construct();
		$this->rules = [];
	}
	/**
	 * @param array|null $data
	 * @param array|null $rules
	 *
	 * @return void
	 */
	public function validate(?array $data = null, ?array $rules = null, ?array $messages = null):void
	{
		$this->data = $data;
		$this->rules = $rules;
		$this->messages = $messages;

		$this->_validation = Validator::validate($this->data, $this->rules);
		$this->_validationSuccess = Validator::checkSuccess($this->_validation);

		if(!$this->_validationSuccess) $this->badRequest($data, $rules);
	}
	/**
	 * @param int $status
	 * @param array|null $data
	 *
	 * @return void
	 */
	public function respond(int $status, ?array $data = null):void
	{
		$this->status = $status;
		$this->data = $data;

		$this->statusMessage = self::STATUS_TEXT[$this->status];

		$this->responseHeaders();
		$this->responseJSON();
	}
	/**
	 * @return void
	 */
	private function responseHeaders():void
	{
		http_response_code($this->status);

		header("Access-Control-Allow-Origin: *");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS, DELETE, PATCH, HEAD, CONNECT, TRACE');
        header("Access-Control-Allow-Headers: X-Requested-With");
		header($_SERVER['SERVER_PROTOCOL'] . ' ' . $this->status . ' ' . $this->statusMessage);
		header('Content-Type: application/json; charset=utf-8');
		header('Date: ' . gmdate('D, d M Y H:i:s T', time()));
		header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');
		header('Expires: Mon, 01 Jul 1900 05:00:00 GMT');
		header('Pragma: no-cache');
        header('Access-Control-Expose-Headers: Content-Disposition, Content-Length, X-Content-Transfer-Id');
		header_remove('x-powered-by');
	}

	/**
	 * @return void
	 */
	private function responseJSON():void
	{
		exit
		(
			json_encode
			(
				[
					'header' =>
					[
						'status' =>
						[
							'code' => $this->status,
							'message' => $this->statusMessage
						],
						'success' => $this->status < 400,
						'error' => $this->status > 399
					],
					'body' =>
					[
						'data' => $this->data,
						'validation' =>
						[
							'result' => $this->_validation,
							'rules' => $this->rules,
							'success' => $this->_validationSuccess
						],
						'messages' => $this->messages
					]
				]
			)
		);
	}
	/**
	 * @param array|null $data
	 * @param array|null $rules
	 *
	 * @return void
	 */
	public function ok(?array $data = null, ?array $rules = null):void
	{
		$this->respond(self::OK, $data, $rules);
	}
	/**
	 * @param array|null $data
	 * @param array|null $rules
	 *
	 * @return void
	 */
	public function created(?array $data = null, ?array $rules = null):void
	{
		$this->respond(self::CREATED, $data, $rules);
	}
	/**
	 * @param array|null $data
	 * @param array|null $rules
	 *
	 * @return void
	 */
	public function badRequest(?array $data = null, ?array $rules = null):void
	{
		$this->respond(self::BAD_REQUEST, $data, $rules);
	}
	/**
	 * @param $exception
	 *
	 * @return void
	 */
	public function internalServeError($exception = null):void
	{
		$this->respond(self::INTERNAL_SERVER_ERROR, ['exception' => $exception], null);
	}
	/**
	 * @param array|null $data
	 * @param array|null $rules
	 *
	 * @return void
	 */
	public function unauthorized(?array $data = null, ?array $rules = null):void
	{
		$this->respond(self::UNAUTHORIZED, $data, $rules);
	}
	/**
	 * @param array|null $data
	 * @param array|null $rules
	 *
	 * @return void
	 */
	public function forbidden(?array $data = null, ?array $rules = null):void
	{
		$this->respond(self::FORBIDDEN, $data, $rules);
	}

	/**
	 * @param array|null $data
	 * @param array|null $rules
	 *
	 * @return void
	 */
	public function accepted(?array $data = null, ?array $rules = null):void
	{
		$this->respond(self::ACCEPTED, $data, $rules);
	}
}
