<?php

namespace Modules\Integration\Repositories;

use Illuminate\Support\Collection;
use Modules\Integration\Contracts\ILogRepository;
use Modules\Integration\Infra\MongoDb;
use MongoDB\Collection as MongoCollection;
use MongoDB\BSON\ObjectID as MongoId;

class LogMongoRepository implements ILogRepository
{
    /**
     * @var Mongo
     */
    private $db;

    /**
     * @var MongoCollection
     */
    private $collection;

    public function __construct()
    {
        $options = [
            'tls' => config('apps.database.mongo.ssl'),
            'tlsCAFile' => config('apps.database.mongo.cert_path')
        ];

        $this->db = MongoDb::getInstance(
            config('apps.database.mongo.uri'),
            $options
        );

        $this->collection = $this->db
            ->selectDatabase(config('apps.database.mongo.db'))
            ->selectCollection(config('apps.database.mongo.collection'));
    }

    public function paginate(
        string $platformId,
        array $where = [],
        array $order = [],
        int $page = 1,
        int $limit = 50
    ): Collection {
        $options = ['sort' => $order];
        if (empty($order)) $options['sort'] = ['_id' => -1];

        $filter = ['metadata.platform_id' => $platformId];

        if (array_key_exists('app_id', $where)) {
            $filter['metadata.app_id'] = (string) $where['app_id'];
        }

        if (array_key_exists('service', $where)) {
            $filter['service'] = $where['service'];
        }

        $data = $this->collection
            ->find($filter, $options);

        return collect($data);
    }

    public function find(string $id, string $platformId)
    {
        $filter = [
            '_id' => new MongoId($id),
            'metadata.platform_id' => $platformId
        ];

        $data = $this->collection
            ->findOne($filter);

        return $data;
    }
}
