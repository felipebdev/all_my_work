<?php
/**
 * Copyright (C) TheOne / ONI - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Tiago Souza <tiagodjf@gmail.com>
 * If you purchased this software, see the license.txt file contained in this source code for more information and possible exceptions.
 */
declare(strict_types=1);

namespace App\Net;

/**
 * Class CURL
 *
 * @class CURL
 * @package App\Net
 */
class CURL
{
	/**
	 * CURL constructor.
	 */
	private function __construct()
	{

	}

	/**
	 * @param string $url
	 * @param array $headers
	 * @param array $options
	 *
	 * @return mixed
	 * @throws \JsonException
	 */
	public static function exec(string $url, array $headers = [], array $options = [])
	{
		$curl = curl_init();

		curl_setopt_array
		(
			$curl,
			[
				CURLOPT_TIMEOUT => 500,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_SSL_VERIFYHOST => false,
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_HTTPHEADER => $headers
			] + $options
		);

		$response = curl_exec($curl);

		curl_close($curl);
		return json_decode($response, true, 512, JSON_THROW_ON_ERROR);
	}
	/**
	 * @param string $url
	 * @param string|array $payload
	 * @param array $headers
	 * @param array $options
	 *
	 * @return mixed
	 * @throws \JsonException
	 */
	public static function post(string $url, $payload, array $headers = [], array $options = [])
	{
		return self::exec($url, $headers, $options + [CURLOPT_CUSTOMREQUEST => 'POST', CURLOPT_POSTFIELDS => is_array($payload) ? json_encode($payload, JSON_THROW_ON_ERROR) : $payload]);
	}
}
