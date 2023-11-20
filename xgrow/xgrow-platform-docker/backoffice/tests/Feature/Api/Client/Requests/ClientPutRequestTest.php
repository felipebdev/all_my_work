<?php

namespace Tests\Feature\Api\Client\Requests;

class ClientPutRequestTest extends ClientRequest
{
    protected string $method = 'putJson';
    protected string $endpoint = '/api/client/1';

    protected function getMethod(): string
    {
        return $this->method;
    }

    protected function getEndpoint(): string
    {
        return $this->endpoint;
    }
}
