<?php
/**
 * Copyright (C) TheOne / ONI - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Tiago Souza <tiagodjf@gmail.com>
 * If you purchased this software, see the license.txt file contained in this source code for more information and possible exceptions.
 */
declare(strict_types=1);

namespace App\Data;

use Elasticsearch\ClientBuilder as ESClientBuilder;
use Elasticsearch\Client as ESClient;
/**
 * Class ElasticSearch
 *
 * @class ElasticSearch
 * @package App\Data
 */
class ElasticSearch
{
	private string $_index;
	private ESClient $_esClient;

	/**
	 * ElasticSearch constructor.
	 *
	 * @param string|null $index
	 */
	public function __construct(?string $index = null)
	{
		$this->_index = $index;
		$this->_esClient = ESClientBuilder::create()->setHosts([
			'user' => env('AUDIT_USER', ''),
			'pass' => env('AUDIT_PASSWORD', ''),
			'host' => env('AUDIT_HOST')
		])->build();
	}

	/**
	 * @param array $params
	 *
	 * @return array
	 */
	private function parseParams(array $params):array
	{
		return array_merge(['index' => $this->_index], $params);
	}
	/**
	 * @param array $params
	 *
	 * @return array|callable
	 */
	public function create(array $params)
	{
		return $this->_esClient->create($this->parseParams($params));
	}
	/**
	 * @param array $params
	 *
	 * @return array|callable
	 */
	public function search(array $params)
	{
		return $this->_esClient->search($this->parseParams($params));
	}

	/**
	 * @param array $params
	 *
	 * @return array|callable
	 */
	public function get(array $params)
	{
		return $this->_esClient->get($this->parseParams($params));
	}

	/**
	 * @param int|null $size
	 * @param int $from
	 *
	 * @return array|callable
	 */
	public function getAll(?int $size = 100, int $from = 0)
	{
		$params = $this->parseParams
		(
			[
				'from' => $from,
				'_source' => ['query' => ['match_all' => []]]
			]
		);
		if($size) $params['size'] = $size;
		return $this->_esClient->search($params);
	}

	/**
	 * @param array $params
	 *
	 * @return array|callable
	 */
	public function getSource(array $params)
	{
		return $this->_esClient->getSource($this->parseParams($params));
	}
	/**
	 * @param array $params
	 *
	 * @return array|callable
	 */
	public function delete(array $params)
	{
		return $this->_esClient->delete($this->parseParams($params));
	}
}
