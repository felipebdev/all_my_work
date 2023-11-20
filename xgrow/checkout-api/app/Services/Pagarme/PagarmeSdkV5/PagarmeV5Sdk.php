<?php

namespace App\Services\Pagarme\PagarmeSdkV5;

use Illuminate\Support\Facades\Log;
use PagarmeCoreApiLib\Http\HttpCallBack;
use PagarmeCoreApiLib\Http\HttpContext;
use PagarmeCoreApiLib\PagarmeCoreApiClient;

use function env;

class PagarmeV5Sdk
{
    private PagarmeCoreApiClient $client;

    public function __construct()
    {
        $this->client = new PagarmeCoreApiClient($this->secret(), '');
    }

    public function getClient(): PagarmeCoreApiClient
    {
        return $this->client;
    }

    /**
     * This is a basic implementation of the API's callback
     *
     * @param  string  $message
     * @return \PagarmeCoreApiLib\Http\HttpCallBack
     */
    public function getCallbackTemplate(string $message): HttpCallBack
    {
        return new HttpCallBack(null, function (HttpContext $httpContext) use ($message) {
            $rawBody = $httpContext->getResponse()->getRawBody();
            Log::debug($message, ['raw_body' => $rawBody]);
        });
    }

    private function secret(): string
    {
        $key = env('MUNDIPAGG_SECRET_KEY');
        if (!$key) {
            Log::critical('MUNDIPAGG_SECRET_KEY env variable is not set');
        }

        return $key;
    }


}
