<?php

namespace Tests\Feature\Api\Email\Requests;

class EmailPostRequestTest extends EmailRequest
{
    protected string $method = 'postJson';
    protected string $endpoint = '/api/email';

    protected function getMethod(): string
    {
        return $this->method;
    }

    protected function getEndpoint(): string
    {
        return $this->endpoint;
    }
}
