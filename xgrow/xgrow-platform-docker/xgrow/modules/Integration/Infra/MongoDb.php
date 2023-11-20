<?php

namespace Modules\Integration\Infra;

use Exception;
use MongoDB\Client;

final class MongoDb
{
    /**
     * @var MongoDB\Client
     */
    private static $client;

    public static function getInstance(
        string $uriConnection,
        array $options = []
    ) {
        try {
            if (self::$client instanceof Client) {
                self::$client->listDatabases();
                return self::$client;
            } else {
                throw new Exception();
            }
        } catch (Exception $exception) {
            return self::newInstance($uriConnection, $options);
        }
    }

    private static function newInstance(
        string $uriConnection,
        array $options = []
    ): Client {
        try {
            self::$client = new Client($uriConnection, $options);
            return self::$client;
        } catch (Exception $exception) {
            throw $exception;
        }
    }
}
