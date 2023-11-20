<?php

namespace Tests\Feature\Api\Platform\Requests;

class PlatformPostRequestTest extends PlatformRequest
{
    protected string $method = 'postJson';
    protected string $endpoint = '/api/platform';

    protected function getMethod(): string
    {
        return $this->method;
    }

    protected function getEndpoint(): string
    {
        return $this->endpoint;
    }

}
