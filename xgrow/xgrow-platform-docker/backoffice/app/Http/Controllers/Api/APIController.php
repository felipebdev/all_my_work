<?php

namespace App\Http\Controllers\Api;

use App\Data\Intl;
use App\Data\Net\HTTPResponse;
use App\Data\Object\TDynamicObject;
use Illuminate\Routing\Controller;
/**
 * @class APIController
 *
 * @property HTTPResponse $response
 */
class APIController extends Controller
{
	use TDynamicObject;
	/**
	 * @var \App\Data\Net\HTTPResponse
	 */
	private HTTPResponse $_response;
	/**
	 * @constructor
	 */
	public function __construct()
	{
		$this->_response = new HTTPResponse();
		Intl::ptBR();
	}

	/**
	 * @return \App\Data\Net\HTTPResponse
	 */
	public function getResponse(): HTTPResponse
	{
		return $this->_response;
	}
}