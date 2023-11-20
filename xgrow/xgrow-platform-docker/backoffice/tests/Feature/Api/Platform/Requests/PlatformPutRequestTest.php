<?php

namespace Tests\Feature\Api\Platform\Requests;

class PlatformPutRequestTest extends PlatformRequest
{
    protected string $method = 'putJson';
    protected string $endpoint = '/api/platform/1';

    protected function getMethod(): string
    {
        return $this->method;
    }

    protected function getEndpoint(): string
    {
        return $this->endpoint;
    }

}

