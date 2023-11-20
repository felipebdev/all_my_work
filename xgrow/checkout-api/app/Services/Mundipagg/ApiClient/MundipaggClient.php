<?php

namespace App\Services\Mundipagg\ApiClient;

use MundiAPILib\MundiAPIClient;

use function env;

/**
 * Wrapper around MundiAPIClient to handle auth using env variables
 */
class MundipaggClient
{

    private MundiAPIClient $client;

    public function __construct()
    {
        $this->client = new MundiAPIClient($this->getSecretKey());
    }

    /**
     * @return \MundiAPILib\MundiAPIClient
     */
    public function getClient(): MundiAPIClient
    {
        return $this->client;
    }

    protected function getPublicKey()
    {
        return env('MUNDIPAGG_PUBLIC_KEY');
    }

    protected function getSecretKey()
    {
        return env('MUNDIPAGG_SECRET_KEY');
    }
}
